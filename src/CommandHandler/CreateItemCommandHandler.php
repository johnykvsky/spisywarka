<?php

namespace App\CommandHandler;

use App\Command\CreateItemCommand;
use App\Event\ItemCreatedEvent;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Repository\ItemRepository;
use App\Repository\ItemCollectionRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ItemNotCreatedException;
use Psr\Log\LoggerInterface;
use App\Repository\CategoryRepository;
use App\Repository\CollectionRepository;
use Symfony\Component\Security\Core\Security;

class CreateItemCommandHandler implements CommandHandlerInterface
{
    /**
     * @var ItemRepository
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
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CollectionRepository
     */
    private $collectionRepository;
    /**
     * @var ItemCollectionRepository
     */
    private $itemCollectionRepository; 
    /**
     * @var Security
     */
    private $security;

    /**
     * @param MessageBusInterface $eventBus
     * @param ItemRepository $repository
     * @param LoggerInterface $logger
     * @param CategoryRepository $categoryRepository
     * @param CollectionRepository $collectionRepository
     * @param ItemCollectionRepository $itemCollectionRepository
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        ItemRepository $repository, 
        LoggerInterface $logger,
        CategoryRepository $categoryRepository,
        CollectionRepository $collectionRepository,
        ItemCollectionRepository $itemCollectionRepository,
        Security $security
    ) {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->categoryRepository = $categoryRepository;
        $this->collectionRepository = $collectionRepository;
        $this->itemCollectionRepository = $itemCollectionRepository;
        $this->security = $security;
    }

    /**
     * @param CreateItemCommand $command
     */
    public function __invoke(CreateItemCommand $command)
    {
        try {
            $category = $this->categoryRepository->getCategory($command->getCategoryId());
            $user = $this->security->getUser();
            
            $item = new Item(
                $command->getId(),
                $command->getName(),
                $category,
                $command->getYear(),
                $command->getFormat(),
                $command->getAuthor(),
                $command->getPublisher(),
                $command->getDescription(),
                $command->getStore(),
                $command->getUrl(),
                $user
                );
   
            if (null !== $command->getCollections()) {
                $this->handleItemCollections($item, $command->getCollections());
            }      
            $this->repository->save($item);
            $this->eventBus->dispatch(new ItemCreatedEvent($command->getId()->toString()));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ItemNotCreatedException('Item was not created: '.$e->getMessage());
        }
    }

    /**
     * @param Item $item
     * @param array $collections
     */
    public function handleItemCollections(Item $item, array $collections): void
    {
        foreach ($collections as $collectionId) {
            $collection = $this->collectionRepository->getCollection($collectionId);
            $itemCollectionEntity = new ItemCollection($item, $collection);
            $item->getCollections()->add($itemCollectionEntity);
        }
    }
}
