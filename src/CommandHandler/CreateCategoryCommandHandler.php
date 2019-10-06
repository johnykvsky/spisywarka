<?php

namespace App\CommandHandler;

use App\Command\CreateCategoryCommand;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CategoryNotCreatedException;
use Psr\Log\LoggerInterface;

class CreateCategoryCommandHandler implements CommandHandlerInterface
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
     * @param CreateCategoryCommand $command
     */
    public function __invoke(CreateCategoryCommand $command)
    {
        try {
            $category = new Category(
                $command->getId(),
                $command->getName(),
                $command->getDescription()
                );
            $this->repository->save($category);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CategoryNotCreatedException('Category was not created: '.$e->getMessage());
        }

    }

}
