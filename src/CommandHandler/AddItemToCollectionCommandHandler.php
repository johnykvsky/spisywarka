<?php

namespace App\CommandHandler;

use App\Command\AddItemToCollectionCommand;
use App\Repository\ItemCollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\CollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use Psr\Log\LoggerInterface;
use App\Entity\ItemCollection;

class AddItemToCollectionCommandHandler implements CommandHandlerInterface
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
     * @param AddItemToCollectionCommand $command
     */
    public function __invoke(AddItemToCollectionCommand $command)
    {
        try {
             $item = $this->itemRepository->getItem($command->getItemId());
             $collection = $this->collectionRepository->getCollection($command->getCollectionId());
             
             if ($item->isInCollection($collection)) {
                 return;
             }
             
             $itemCollection = new ItemCollection($item, $collection);
             $this->repository->save($itemCollection);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ItemNotUpdatedException('Item was not added to collection: '.$e->getMessage());
        }
    }

}
