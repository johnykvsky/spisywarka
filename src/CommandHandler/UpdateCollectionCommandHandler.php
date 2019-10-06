<?php

namespace App\CommandHandler;

use App\Command\UpdateCollectionCommand;
//use App\Entity\Collection;
use App\Repository\CollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CollectionNotUpdatedException;
use Psr\Log\LoggerInterface;

class UpdateCollectionCommandHandler implements CommandHandlerInterface
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
     * @param UpdateCollectionCommand $command
     */
    public function __invoke(UpdateCollectionCommand $command)
    {
        try {
            $collection = $this->repository->getCollection($command->getId());
            $collection->setName($command->getName());
            $collection->setDescription($command->getDescription());
            $this->repository->save($collection);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CollectionNotUpdatedException('Collection was not udated: '.$e->getMessage());
        }
    }
}
