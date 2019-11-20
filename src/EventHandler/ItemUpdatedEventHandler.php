<?php

namespace App\EventHandler;

use App\Event\ItemUpdatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ItemUpdatedEventHandler
 * @package App\EventHandler
 */
class ItemUpdatedEventHandler implements EventHandlerInterface
{
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
     * @param LoggerInterface $logger
     */
    public function __construct(MessageBusInterface $eventBus, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->logger = $logger;
    }

    /**
     * @param ItemUpdatedEvent $event
     */
    public function __invoke(ItemUpdatedEvent $event)
    {
        $this->logger->info('Item updated event: '. $event->getId());
    }
}
