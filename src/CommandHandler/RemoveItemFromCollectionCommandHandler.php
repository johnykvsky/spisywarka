<?php

namespace App\CommandHandler;

use App\Command\RemoveItemFromCollectionCommand;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\CollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use Psr\Log\LoggerInterface;

class RemoveItemFromCollectionCommandHandler implements CommandHandlerInterface
{
    /**
     * @var ItemCollectionRepository
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
     * @var ItemRepository
     */
    private $itemRepository;
    /**
     * @var CollectionRepository
     */
    private $collectionRepository;

    /**
     * @param MessageBusInterface $eventBus
     * @param ItemCollectionRepository $repository
     * @param LoggerInterface $logger
     * @param ItemRepository $itemRepository
     * @param CollectionRepository $collectionRepository
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        ItemCollectionRepository $repository, 
        LoggerInterface $logger,
        ItemRepository $itemRepository,
        CollectionRepository $collectionRepository
    )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->itemRepository = $itemRepository;
        $this->collectionRepository = $collectionRepository;
    }

    /**
     * @param RemoveItemFromCollectionCommand $command
     */
    public function __invoke(RemoveItemFromCollectionCommand $command)
    {
        try {
             $item = $this->itemRepository->getItem($command->getItemId());
             $collection = $this->collectionRepository->getCollection($command->getCollectionId());
             
             if (!$item->isInCollection($collection)) {
                 return;
             }
             
             $itemCollection = $this->repository->findItemCollection($item, $collection);
             
             if (!$itemCollection) {
                 return;
             }
             
             $this->repository->delete($itemCollection);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ItemNotUpdatedException('Item was not removed from collection: '.$e->getMessage());
        }
    }

}
