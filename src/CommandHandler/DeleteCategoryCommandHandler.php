<?php

namespace App\CommandHandler;

use App\Command\DeleteCategoryCommand;
use App\Repository\CategoryRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CategoryNotDeletedException;
use Psr\Log\LoggerInterface;

class DeleteCategoryCommandHandler implements CommandHandlerInterface
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
     * @param DeleteCategoryCommand $command
     */
    public function __invoke(DeleteCategoryCommand $command)
    {
        try {
            $category = $this->repository->getCategory($command->getId());
            
            if ($category->hasItems()) {
                throw new CategoryNotDeletedException('Category has items assigned');
            }
            
            $this->repository->delete($category);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CategoryNotDeletedException('Category was not deleted: '.$e->getMessage());
        }
    }
}
