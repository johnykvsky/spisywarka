<?php

namespace App\EventHandler;

use App\Event\ItemCreatedEvent;
//use App\Entity\Item;
use App\Repository\ItemRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ItemCreatedEventHandler
 * @package App\EventHandler
 */
class ItemCreatedEventHandler implements EventHandlerInterface
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
     * @param MessageBusInterface $eventBus
     * @param ItemRepository $repository
     */
    public function __construct(MessageBusInterface $eventBus, ItemRepository $repository, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param ItemCreatedEvent $event
     */
    public function __invoke(ItemCreatedEvent $event)
    {
        $this->logger->info('Item created event: '. $event->getId());
    }
}
