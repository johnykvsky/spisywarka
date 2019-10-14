<?php

namespace App\EventHandler;

use App\Event\ItemCreatedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ItemCreatedEventHandler
 * @package App\EventHandler
 */
class ItemCreatedEventHandler implements EventHandlerInterface
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
     * @param ItemCreatedEvent $event
     */
    public function __invoke(ItemCreatedEvent $event)
    {
        $this->logger->info('Item created event: '. $event->getId());
    }
}
