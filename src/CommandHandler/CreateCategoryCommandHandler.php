<?php

namespace App\CommandHandler;

use App\Command\CreateCategoryCommand;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\CategoryNotCreatedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

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
     * @var Security
     */
    private $security;

    /** 
     * @param MessageBusInterface $eventBus
     * @param CategoryRepository $repository
     * @param LoggerInterface $logger
     * @param Security $security
     */
    public function __construct(MessageBusInterface $eventBus, CategoryRepository $repository, LoggerInterface $logger, Security $security)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->security = $security;
    }

    /**
     * @param CreateCategoryCommand $command
     */
    public function __invoke(CreateCategoryCommand $command)
    {
        try {
            $user = $this->security->getUser();

            $category = new Category(
                $command->getId(),
                $command->getName(),
                $command->getDescription(),
                $user
                );
            $this->repository->save($category);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CategoryNotCreatedException('Category was not created: '.$e->getMessage());
        }
    }
}
