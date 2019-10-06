<?php

namespace App\CommandHandler;

use App\Command\RemoveItemFromCategoryCommand;
use App\Repository\ItemCategoryRepository;
use App\Repository\ItemRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ItemNotUpdatedException;
use Psr\Log\LoggerInterface;

class RemoveItemFromCategoryCommandHandler implements CommandHandlerInterface
{
    /**
     * @var ItemCategoryRepository
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
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param MessageBusInterface $eventBus
     * @param ItemCategoryRepository $repository
     * @param LoggerInterface $logger
     * @param ItemRepository $itemRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        ItemCategoryRepository $repository, 
        LoggerInterface $logger,
        ItemRepository $itemRepository,
        CategoryRepository $categoryRepository
    )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->itemRepository = $itemRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param RemoveItemFromCategoryCommand $command
     */
    public function __invoke(RemoveItemFromCategoryCommand $command)
    {
        try {
             $item = $this->itemRepository->getItem($command->getItemId());
             $category = $this->categoryRepository->getCategory($command->getCategoryId());
             
             if (!$item->isInCategory($category)) {
                 return;
             }
             
             $itemCategory = $this->repository->findItemCategory($item, $category);
             
             if (!$itemCategory) {
                 return;
             }
             $this->repository->delete($itemCategory);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ItemNotUpdatedException('Item was not removed from category: '.$e->getMessage());
        }
    }

}
