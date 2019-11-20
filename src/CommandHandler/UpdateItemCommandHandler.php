<?php

namespace App\CommandHandler;

use App\Command\UpdateItemCommand;
use App\Repository\ItemRepository;
use App\Repository\CategoryRepository;
use App\Repository\CollectionRepository;
use App\Repository\ItemCollectionRepository;
use App\Repository\Exception\ItemCollectionNotFoundException;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use Psr\Log\LoggerInterface;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Event\ItemUpdatedEvent;

class UpdateItemCommandHandler implements CommandHandlerInterface
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
     * @var ItemCollectionRepository
     */
    private $itemCollectionRepository; 
    /**
     * @var CollectionRepository
     */
    private $collectionRepository; 

    /**
     * @param MessageBusInterface $eventBus
     * @param ItemRepository $repository
     * @param LoggerInterface $logger
     * @param CategoryRepository $categoryRepository
     * @param ItemCollectionRepository $itemCollectionRepository
     * @param CollectionRepository $collectionRepository
     */
    public function __construct(
        MessageBusInterface $eventBus,
        ItemRepository $repository,
        LoggerInterface $logger,
        CategoryRepository $categoryRepository,
        ItemCollectionRepository $itemCollectionRepository,
        CollectionRepository $collectionRepository
    )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->categoryRepository = $categoryRepository;
        $this->itemCollectionRepository = $itemCollectionRepository;
        $this->collectionRepository = $collectionRepository;
    }

    /**
     * @param UpdateItemCommand $command
     */
    public function __invoke(UpdateItemCommand $command)
    {
        try {
             $item = $this->repository->getItem($command->getId());
             $category = $this->categoryRepository->getCategory($command->getCategoryId());

             $item->setName($command->getName());
             $item->setCategory($category);
             $item->setYear($command->getYear());
             $item->setFormat($command->getFormat());
             $item->setAuthor($command->getAuthor());
             $item->setPublisher($command->getPublisher());
             $item->setDescription($command->getDescription());
             $item->setStore($command->getStore());
             $item->setUrl($command->getUrl());
             
            $this->repository->save($item);

            if (null !== $command->getCollections()) {
                $this->handleItemCollections($item, $command->getCollections());
            }
            $this->eventBus->dispatch(new ItemUpdatedEvent($command->getId()->toString()));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ItemNotUpdatedException('Item was not updated: '.$e->getMessage());
        }
    }

    /**
     * @param Item $item
     * @param array $collections
     */
    public function handleItemCollections(Item $item, array $collections): void
    {
        $item->getCollections()->clear();
        foreach ($collections as $collectionId) {
            $collection = $this->collectionRepository->getCollection($collectionId);
            try {
                $itemCollectionEntity = $this->itemCollectionRepository->getItemCollection($item, $collection);
                $item->getCollections()->add($itemCollectionEntity);
            } catch (ItemCollectionNotFoundException $e) {
                $itemCollectionEntity = new ItemCollection($item, $collection);
                $item->getCollections()->add($itemCollectionEntity);
            }
        }
        $this->repository->save($item);
    }
}
