<?php

namespace App\CommandHandler;

use App\Command\DeleteCollectionCommand;
use App\Repository\CollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CollectionNotDeletedException;
use Psr\Log\LoggerInterface;

class DeleteCollectionCommandHandler implements CommandHandlerInterface
{
    /**
     * @var CollectionRepository
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
     * @param MessageBusInterface $eventBus
     * @param CollectionRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(MessageBusInterface $eventBus, CollectionRepository $repository, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param DeleteCollectionCommand $command
     */
    public function __invoke(DeleteCollectionCommand $command)
    {
        try {
            $collection = $this->repository->getCollection($command->getId());
             
            if ($collection->hasItems()) {
                throw new CollectionNotDeletedException('Collection has items assigned');
            }
             
            $this->repository->delete($collection);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CollectionNotDeletedException('Collection was not deleted: '.$e->getMessage());
        }
    }
}
