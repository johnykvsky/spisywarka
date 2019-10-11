<?php

namespace App\Controller\Api;

use App\Command\CreateCollectionCommand;
use App\Command\DeleteCollectionCommand;
use App\Command\UpdateCollectionCommand;
use App\Error\ApiError;
use App\Repository\Exception\CollectionNotFoundException;
use App\Repository\CollectionRepository;
use App\Request\CreateCollectionRequest;
use App\Request\UpdateCollectionRequest;
use App\Traits\JsonErrorResponse;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Swagger\Annotations as SWG;
use App\CommandHandler\Exception\CollectionNotDeletedException;

class CollectionController extends AbstractController
{
    use JsonErrorResponse;

    /**
     * @var MessageBusInterface
     */
    private $commandBus;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CollectionRepository
     */
    private $repository;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param CollectionRepository $repository
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        CollectionRepository $repository
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * @Route("/api/collections", name="collection_create", methods={"POST"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     * 
     * @SWG\Tag(name="Collections")
     * @SWG\Post(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"name"},
     *              @SWG\Property(property="name", type="string", maxLength=255),
     *              @SWG\Property(property="description", type="string"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Returns ID of created Collection",
     *     @SWG\Schema(
     *          @SWG\Property(property="id", type="string", format="UUID")
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Collection was not created"
     * )
     * 
     * @param CreateCollectionRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function create(CreateCollectionRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            $response = $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for create Collection',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
            );
        }

        try {
            $id = Uuid::uuid4();
            $command = new CreateCollectionCommand(
                $id,
                $request->getName(),
                $request->getDescription()
            );
            $this->commandBus->dispatch($command);
            $response = $this->json(['id' => $id], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $response;
    }

    /**
     * @Route("/api/collections/{id}", name="collection_get", methods={"GET"})
     *
     * @SWG\Tag(name="Collections")
     * @SWG\Get(
     *     @SWG\Parameter(name="id", in="path", type="string", format="UUID"),
     *     @SWG\Response(
     *          response="200", 
     *          description="Collection details",
     *          @SWG\Schema(
     *              @SWG\Property(property="id", type="string") ,
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="description", type="string")
     *         )
     *     ),
     *     @SWG\Response(response="404", description="Collection not found"),
     *     @SWG\Response(response="422", description="Validation failed")
     * )
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function getCollectionAction(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $response = $this->json($this->repository->getCollection($uuid));
        } catch (\Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
            return $this->jsonError(ApiError::ENTITY_UUID_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (CollectionNotFoundException $e) {
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                'Unknown Collection',
                Response::HTTP_NOT_FOUND
                );
        }

        return $response;
    }

    /**
     * @Route("/api/collections", name="collection_update", methods={"PATCH"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     * 
     * @SWG\Tag(name="Collections")
     * @SWG\Patch(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"id", "name"},
     *              @SWG\Property(property="id", type="string", maxLength=255),
     *              @SWG\Property(property="name", type="string", maxLength=255),
     *              @SWG\Property(property="description", type="string"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Collection has been updated",
     *     @SWG\Schema(
     *              @SWG\Property(property="id", type="string", maxLength=255),
     *              @SWG\Property(property="name", type="string", maxLength=255),
     *              @SWG\Property(property="description", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Collection was not updated"
     * )
     * 
     * @param UpdateCollectionRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function update(UpdateCollectionRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            $response = $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for update Collection',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
            );
        }

        try {
            $command = new UpdateCollectionCommand(
                $request->getId(),
                $request->getName(),
                $request->getDescription()
            );
            $this->commandBus->dispatch($command);
            $collection = $this->repository->getCollection($command->getId());
            $response = $this->json($collection, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $response;
    }

    /**
     * Delete Collection
     *
     * @SWG\Tag(name="Collections")
     * @SWG\Response(
     *     response="204",
     *     description="Collection was deleted"
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Collection not found",
     * )
     *
     * @SWG\Response(
     *     response="422",
     *     description="Collection was not removed",
     * )
     *
     * @Route("/api/collections/{id}", name="collection_delete", methods={"DELETE"})
     * @param string $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $command = new DeleteCollectionCommand($uuid);
            $this->commandBus->dispatch($command);
            $response = $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
            $response = $this->jsonError(ApiError::ENTITY_UUID_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        } catch (CollectionNotFoundException $e) {
            $response = $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
                );
        } catch (CollectionNotDeletedException $e) {
            $response = $this->jsonError(ApiError::ENTITY_DELETE_ERROR,
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
                );
        }
        
        return $response;
    }
}
