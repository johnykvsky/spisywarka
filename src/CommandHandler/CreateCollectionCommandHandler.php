<?php

namespace App\CommandHandler;

use App\Command\CreateCollectionCommand;
use App\Entity\Collection;
use App\Repository\CollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CollectionNotCreatedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

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
     * @var Security
     */
    private $security;

    /**
     * @param MessageBusInterface $eventBus
     * @param CollectionRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(MessageBusInterface $eventBus, CollectionRepository $repository, LoggerInterface $logger, Security $security)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->security = $security;
    }

    /**
     * @param CreateCollectionCommand $command
     */
    public function __invoke(CreateCollectionCommand $command)
    {
        try {
            $user = $this->security->getUser();

            $collection = new Collection(
                $command->getId(),
                $command->getName(),
                $command->getDescription(),
                $user
                );
            $this->repository->save($collection);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CollectionNotCreatedException('Collection was not created: '.$e->getMessage());
        }

    }

}
