<?php

namespace App\CommandHandler;

use App\Command\CreateItemCommand;
use App\Event\ItemCreatedEvent;
use App\Entity\Item;
use App\Entity\ItemCategory;
use App\Entity\ItemCollection;
use App\Repository\ItemRepository;
use App\Repository\ItemCategoryRepository;
use App\Repository\ItemCollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ItemNotCreatedException;
use Psr\Log\LoggerInterface;
use App\Repository\CategoryRepository;
use App\Repository\CollectionRepository;

class CreateItemCommandHandler implements CommandHandlerInterface
{
    /**
     * @var ItemRepository
     */
    private $repository;    
    /**
     * @var MessageBusInterface
     */
    private $eventBus;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CollectionRepository
     */
    private $collectionRepository;
    /**
     * @var ItemCategoryRepository
     */
    private $itemCategoryRepository; 
    /**
     * @var ItemCollectionRepository
     */
    private $itemCollectionRepository; 

    /**
     * @param MessageBusInterface $eventBus
     * @param ItemRepository $repository
     * @param LoggerInterface $logger
     * @param CategoryRepository $categoryRepository
     * @param CollectionRepository $collectionRepository
     * @param ItemCategoryRepository $itemCategoryRepository
     * @param ItemCollectionRepository $itemCollectionRepository
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        ItemRepository $repository, 
        LoggerInterface $logger,
        CategoryRepository $categoryRepository,
        CollectionRepository $collectionRepository,
        ItemCategoryRepository $itemCategoryRepository,
        ItemCollectionRepository $itemCollectionRepository)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->categoryRepository = $categoryRepository;
        $this->collectionRepository = $collectionRepository;
        $this->itemCategoryRepository = $itemCategoryRepository;
        $this->itemCollectionRepository = $itemCollectionRepository;
    }

    /**
     * @param CreateItemCommand $command
     */
    public function __invoke(CreateItemCommand $command)
    {
        try {
            $item = new Item(
                $command->getId(),
                $command->getName(),
                $command->getYear(),
                $command->getFormat(),
                $command->getAuthor(),
                $command->getPublisher(),
                $command->getDescription(),
                $command->getStore(),
                $command->getUrl()
                );

            if (null !== $command->getCategories()) {
                $this->handleItemCategories($item, $command->getCategories());
            }      
            if (null !== $command->getCollections()) {
                $this->handleItemCollections($item, $command->getCollections());
            }      
            $this->repository->save($item);
            $this->eventBus->dispatch(new ItemCreatedEvent($command->getId()->toString()));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ItemNotCreatedException('Item was not created: '.$e->getMessage());
        }
    }

    /**
     * @param Item $item
     * @param array $categories
     */
    public function handleItemCategories(Item $item, array $categories): void
    {
        foreach ($categories as $categoryId) {
            $category = $this->categoryRepository->getCategory($categoryId);
            $itemCategoryEntity = new ItemCategory($item, $category);
            $item->getCategories()->add($itemCategoryEntity);
        }
    }

    /**
     * @param Item $item
     * @param array $collections
     */
    public function handleItemCollections(Item $item, array $collections): void
    {
        foreach ($collections as $collectionId) {
            $collection = $this->collectionRepository->getCollection($collectionId);
            $itemCollectionEntity = new ItemCollection($item, $collection);
            $item->getCollections()->add($itemCollectionEntity);
        }
    }
}
