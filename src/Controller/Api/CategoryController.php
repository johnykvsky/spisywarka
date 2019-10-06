<?php

namespace App\Controller\Api;

use App\Command\CreateCategoryCommand;
use App\Command\UpdateCategoryCommand;
use App\Command\DeleteCategoryCommand;
use App\Error\ApiError;
use App\Repository\Exception\CategoryNotFoundException;
use App\Repository\CategoryRepository;
use App\Request\CreateCategoryRequest;
use App\Request\UpdateCategoryRequest;
//use App\Request\DeleteCategoryRequest;
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
//use App\CommandHandler\Exception\CategoryNotCreatedException;
use App\CommandHandler\Exception\CategoryNotDeletedException;
//use App\CommandHandler\Exception\CategoryNotUpdatedException;

class CategoryController extends AbstractController
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
     * @var CategoryRepository
     */
    private $repository;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param CategoryRepository $repository
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        CategoryRepository $repository
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * @Route("/api/categories", name="category_create", methods={"POST"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     * 
     * @SWG\Tag(name="Categories")
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
     *     description="Returns ID of created Category",
     *     @SWG\Schema(
     *          @SWG\Property(property="id", type="string", format="UUID")
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Category was not created"
     * )
     * 
     * @param CreateCategoryRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function create(CreateCategoryRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            $response = $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for create Category',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
            );
        }

        try {
            $id = Uuid::uuid4();
            $command = new CreateCategoryCommand(
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
     * @Route("/api/categories/{id}", name="category_get", methods={"GET"})
     *
     * @SWG\Tag(name="Categories")
     * @SWG\Get(
     *     @SWG\Parameter(name="id", in="path", type="string", format="UUID"),
     *     @SWG\Response(
     *          response="200", 
     *          description="Category details",
     *          @SWG\Schema(
     *              @SWG\Property(property="id", type="string") ,
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="description", type="string")
     *         )
     *     ),
     *     @SWG\Response(response="404", description="Category not found"),
     *     @SWG\Response(response="422", description="Validation failed")
     * )
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function getCategoryAction(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $response = $this->json($this->repository->getCategory($uuid));
        } catch (\Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
            return $this->jsonError(ApiError::ENTITY_UUID_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (CategoryNotFoundException $e) {
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                'Unknown Category',
                Response::HTTP_NOT_FOUND
                );
        }

        return $response;
    }

    /**
     * @Route("/api/categories", name="category_update", methods={"PATCH"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     * 
     * @SWG\Tag(name="Categories")
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
     *     description="Category has been updated",
     *     @SWG\Schema(
     *              @SWG\Property(property="id", type="string", maxLength=255),
     *              @SWG\Property(property="name", type="string", maxLength=255),
     *              @SWG\Property(property="description", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Category was not updated"
     * )
     * 
     * @param UpdateCategoryRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            $response = $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for update Category',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
            );
        }

        try {
            $command = new UpdateCategoryCommand(
                $request->getId(),
                $request->getName(),
                $request->getDescription()
            );
            $this->commandBus->dispatch($command);
            $category = $this->repository->getCategory($command->getId());
            $response = $this->json($category, Response::HTTP_OK);
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
     * Delete Category
     *
     * @SWG\Tag(name="Categories")
     * @SWG\Response(
     *     response="204",
     *     description="Category was deleted"
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Category not found",
     * )
     *
     * @SWG\Response(
     *     response="422",
     *     description="Category was not removed",
     * )
     *
     * @Route("/api/categories/{id}", name="category_delete", methods={"DELETE"})
     * @param string $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $command = new DeleteCategoryCommand($uuid);
            $this->commandBus->dispatch($command);
            $response = $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
            $response = $this->jsonError(ApiError::ENTITY_UUID_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        } catch (CategoryNotFoundException $e) {
            $response = $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
                );
        } catch (CategoryNotDeletedException $e) {
            $response = $this->jsonError(ApiError::ENTITY_DELETE_ERROR,
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
                );
        }
        
        return $response;
    }
}
