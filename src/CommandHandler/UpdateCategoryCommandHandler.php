<?php

namespace App\CommandHandler;

use App\Command\UpdateCategoryCommand;
use App\Repository\CategoryRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CategoryNotUpdatedException;
use Psr\Log\LoggerInterface;

class UpdateCategoryCommandHandler implements CommandHandlerInterface
{
    /**
     * @var CategoryRepository
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
     * @param CategoryRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(MessageBusInterface $eventBus, CategoryRepository $repository, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param UpdateCategoryCommand $command
     */
    public function __invoke(UpdateCategoryCommand $command)
    {
        try {
            $category = $this->repository->getCategory($command->getId());
            $category->setName($command->getName());
            $category->setDescription($command->getDescription());
            $this->repository->save($category);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CategoryNotUpdatedException('Category was not udated: '.$e->getMessage());
        }

    }

}
