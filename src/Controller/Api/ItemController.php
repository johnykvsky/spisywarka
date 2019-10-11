<?php

namespace App\Controller\Api;

use App\Command\CreateItemCommand;
use App\Command\UpdateItemCommand;
use App\Command\DeleteItemCommand;
use App\Command\AddItemToCategoryCommand;
use App\Command\RemoveItemFromCategoryCommand;
use App\Error\ApiError;
use App\Repository\Exception\ItemNotFoundException;
use App\Repository\ItemRepository;
use App\Request\CreateItemRequest;
use App\Request\UpdateItemRequest;
use App\Request\AddItemToCategoryRequest;
use App\Traits\JsonErrorResponse;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Swagger\Annotations as SWG;
//use App\CommandHandler\Exception\ItemNotCreatedException;
use App\CommandHandler\Exception\ItemNotDeletedException;
use App\Request\RemoveItemFromCategoryRequest;
use App\Request\AddItemToCollectionRequest;
use App\Command\AddItemToCollectionCommand;
use App\Request\RemoveItemFromCollectionRequest;
use App\Command\RemoveItemFromCollectionCommand;
//use App\CommandHandler\Exception\ItemNotUpdatedException;
use Nelmio\ApiDocBundle\Annotation\Model;

class ItemController extends AbstractController
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
     * @var ItemRepository
     */
    private $repository;

    /**
     * @param MessageBusInterface $commandBus
     * @param LoggerInterface $logger
     * @param ItemRepository $repository
     */
    public function __construct(
        MessageBusInterface $commandBus,
        LoggerInterface $logger,
        ItemRepository $repository
    )
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    /**
     * @Route("/api/items", name="item_create", methods={"POST"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     * 
     * @SWG\Tag(name="Items")
     * @SWG\Post(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"name"},
     *              @SWG\Property(property="name", type="string", maxLength=255),
     *              @SWG\Property(property="category", type="string", format="UUID") ,
     *              @SWG\Property(property="year", type="integer"),
     *              @SWG\Property(property="format", type="string"),
     *              @SWG\Property(property="author", type="string"),
     *              @SWG\Property(property="publisher", type="string"),
     *              @SWG\Property(property="description", type="string"),
     *              @SWG\Property(property="store", type="string"),
     *              @SWG\Property(property="url", type="string"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Returns ID of created Item",
     *     @SWG\Schema(
     *          @SWG\Property(property="id", type="string", format="UUID")
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Item was not created"
     * )
     * 
     * @param CreateItemRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function create(CreateItemRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            return $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for create Item',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
            );
        }

        try {
            $id = Uuid::uuid4();
            $command = new CreateItemCommand(
                $id,
                $request->getName(),
                $request->getCategory(),
                $request->getYear(),
                $request->getFormat(),
                $request->getAuthor(),
                $request->getPublisher(),
                $request->getDescription(),
                $request->getStore(),
                $request->getUrl(),
                null
            );
            $this->commandBus->dispatch($command);
            return $this->json(['id' => $id], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    /**
     * @Route("/api/items/{id}", name="item_get", methods={"GET"})
     *
     * @SWG\Tag(name="Items")
     * @SWG\Get(
     *     @SWG\Parameter(name="id", in="path", type="string", format="UUID"),
     *     @SWG\Response(
     *          response="200", 
     *          description="Item details",
     *          @SWG\Schema(
     *              @SWG\Property(property="id", type="string", format="UUID") ,
     *              @SWG\Property(property="name", type="string"),
     *              @SWG\Property(property="category", type="string", format="UUID") ,
     *              @SWG\Property(property="year", type="integer"),
     *              @SWG\Property(property="format", type="string"),
     *              @SWG\Property(property="author", type="string"),
     *              @SWG\Property(property="publisher", type="string"),
     *              @SWG\Property(property="description", type="string"),
     *              @SWG\Property(property="store", type="string"),
     *              @SWG\Property(property="url", type="string")
     *         )
     *     ),
     *     @SWG\Response(response="404", description="Item not found"),
     *     @SWG\Response(response="422", description="Validation failed")
     * )
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function getItemAction(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            return $this->json($this->repository->getItem($uuid));
        } catch (\Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
            return $this->jsonError(ApiError::ENTITY_UUID_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (ItemNotFoundException $e) {
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
            );
        }
        catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
                );
        }
    }
    
    /**
     * @Route("/api/items", name="item_update", methods={"PATCH"})
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     *
     * @SWG\Tag(name="Items")
     * @SWG\Patch(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"id", "name"},
     *              @SWG\Property(property="id", type="string", format="UUID"),
     *              @SWG\Property(property="name", type="string", maxLength=255),
     *              @SWG\Property(property="category", type="string", format="UUID") ,
     *              @SWG\Property(property="year", type="integer"),
     *              @SWG\Property(property="format", type="string"),
     *              @SWG\Property(property="author", type="string"),
     *              @SWG\Property(property="publisher", type="string"),
     *              @SWG\Property(property="description", type="string"),
     *              @SWG\Property(property="store", type="string"),
     *              @SWG\Property(property="url", type="string"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Item has been updated",
     *     @SWG\Schema(
     *              @SWG\Property(property="id", type="string", format="UUID"),
     *              @SWG\Property(property="name", type="string", maxLength=255),
     *              @SWG\Property(property="category", type="string", format="UUID") ,
     *              @SWG\Property(property="year", type="integer"),
     *              @SWG\Property(property="format", type="string"),
     *              @SWG\Property(property="author", type="string"),
     *              @SWG\Property(property="publisher", type="string"),
     *              @SWG\Property(property="description", type="string"),
     *              @SWG\Property(property="store", type="string"),
     *              @SWG\Property(property="url", type="string"),
     *     )
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Item was not updated"
     * )
     *
     * @param UpdateItemRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function update(UpdateItemRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            return $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for update Item',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
                );
        }
        
        try {
            $command = new UpdateItemCommand(
                $request->getId(),
                $request->getName(),
                $request->getCategory(),
                $request->getYear(),
                $request->getFormat(),
                $request->getAuthor(),
                $request->getPublisher(),
                $request->getDescription(),
                $request->getStore(),
                $request->getUrl(),
                null
                );
            $this->commandBus->dispatch($command);
            $item = $this->repository->getItem($command->getId());
            return $this->json($item, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }
    }

    /**
     * Delete Item
     *
     * @SWG\Tag(name="Items")
     * @SWG\Response(
     *     response="204",
     *     description="Item was deleted"
     * )
     * @SWG\Response(
     *     response="404",
     *     description="Item not found",
     * )
     *
     * @SWG\Response(
     *     response="422",
     *     description="Item was not removed",
     * )
     *
     * @Route("/api/items/{id}", name="items-delete", methods={"DELETE"})
     * @param string $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $command = new DeleteItemCommand($uuid);
            $this->commandBus->dispatch($command);
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Ramsey\Uuid\Exception\InvalidUuidStringException $e) {
            return $this->jsonError(ApiError::ENTITY_UUID_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        } catch (ItemNotFoundException $e) {
            return $this->jsonError(ApiError::ENTITY_READ_ERROR,
                $e->getMessage(),
                Response::HTTP_NOT_FOUND
                );
        } catch (ItemNotDeletedException $e) {
            return $this->jsonError(ApiError::ENTITY_DELETE_ERROR,
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
                );
        }
    }    

    /**
     * @Route("/api/item/collection", name="add_item_to_collection", methods={"POST"})
     *
     * @SWG\Tag(name="Items")
     * @SWG\Post(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"name"},
     *              @SWG\Property(property="itemId", type="string", format="UUID"),
     *              @SWG\Property(property="categoryId", type="string", format="UUID"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Item added to collection"
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Item was not added to collection"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Item or Collection nor found"
     * )
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     *
     * @param AddItemToCollectionRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function addItemToCollection(AddItemToCollectionRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            return  $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for create Item',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
                );
        }
        
        try {
            $command = new AddItemToCollectionCommand(
                $request->getItemId(),
                $request->getCollectionId()
                );
            $this->commandBus->dispatch($command);
            return $this->json(null, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }
    }
    
    /**
     * @Route("/api/item/collection", name="remove_item_from_collection", methods={"DELETE"})
     *
     * @SWG\Tag(name="Items")
     * @SWG\Delete(
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          format="application/json",
     *          @SWG\Schema(
     *              required={"name"},
     *              @SWG\Property(property="itemId", type="string", format="UUID", format="UUID"),
     *              @SWG\Property(property="categoryId", type="string", format="UUID", format="UUID"),
     *          )
     *     )
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Item removed from collection"
     * )
     * @SWG\Response(
     *     response=422,
     *     description="Item was not delete from collection"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Item or Collection not found"
     * )
     *
     * @ParamConverter("request", converter="fos_rest.request_body")
     *
     * @param RemoveItemFromCollectionRequest $request
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function removeItemFromCollection(RemoveItemFromCollectionRequest $request, ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        if ($validationErrors->count()) {
            return  $this->jsonError(ApiError::ENTITY_VALIDATION_ERROR,
                'Validations errors for create Item',
                Response::HTTP_BAD_REQUEST,
                $this->parseFormErrors($validationErrors)
                );
        }
        
        try {
            $command = new RemoveItemFromCollectionCommand(
                $request->getItemId(),
                $request->getCollectionId()
                );
            $this->commandBus->dispatch($command);
            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }
    }
    
    /**
     * List items
     *
     * @SWG\Tag(name="Items")
     * @SWG\Response(
     *     response=200,
     *     description="List of all items",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=App\Entity\Item::class))
     *     )
     * )
     *
     * @param Request $request
     * @Route("/api/items", name="items-list", methods={"GET"})
     * @return JsonResponse
     */
    public function getItemsList(Request $request): JsonResponse
    {
        return $this->json($this->repository->listAllItemsForApi());
    }
    
    /**
     * List category items
     *
     * @SWG\Tag(name="Items")
     * @SWG\Response(
     *     response=200,
     *     description="List of category items",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=App\Entity\Item::class))
     *     )
     * )
     *
     * @param Request $request
     * @Route("/api/category/items/{categoryId}", name="items-in-category-list", methods={"GET"})
     * @return JsonResponse
     */
    public function getItemsInCategory(string $categoryId, Request $request): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($categoryId);
            return $this->json($this->repository->getItemsInCategory($uuid));
        } catch (\Exception$e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }
    }
    
    /**
     * List collection items
     *
     * @SWG\Tag(name="Items")
     * @SWG\Response(
     *     response=200,
     *     description="List of collection items",
     *     @SWG\Schema(
     *          type="array",
     *          @SWG\Items(ref=@Model(type=App\Entity\Item::class))
     *     )
     * )
     *
     * @param Request $request
     * @Route("/api/collection/items/{collectionId}", name="items-in-collection-list", methods={"GET"})
     * @return JsonResponse
     */
    public function getItemsInCollection(string $collectionId, Request $request): JsonResponse
    {
        try {
            $uuid = Uuid::fromString($collectionId);
            return $this->json($this->repository->getItemsInCollection($uuid));
        } catch (\Exception$e) {
            $this->logger->critical($e->getMessage());
            return $this->jsonError(ApiError::ENTITY_CREATE_ERROR,
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }
    }
}
