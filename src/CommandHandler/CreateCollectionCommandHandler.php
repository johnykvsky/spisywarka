<?php

namespace App\CommandHandler;

use App\Command\CreateCollectionCommand;
use App\Entity\Collection;
use App\Repository\CollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CollectionNotCreatedException;
use Psr\Log\LoggerInterface;

class CreateCollectionCommandHandler implements CommandHandlerInterface
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
     * @param CreateCollectionCommand $command
     */
    public function __invoke(CreateCollectionCommand $command)
    {
        try {
            $collection = new Collection(
                $command->getId(),
                $command->getName(),
                $command->getDescription()
                );
            $this->repository->save($collection);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CollectionNotCreatedException('Collection was not created: '.$e->getMessage());
        }

    }

}
