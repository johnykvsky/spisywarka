<?php

namespace App\EventHandler;

use App\Event\ItemDeletedEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ItemDeletedEventHandler
 * @package App\EventHandler
 */
class ItemDeletedEventHandler implements EventHandlerInterface
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
     * @param ItemDeletedEvent $event
     */
    public function __invoke(ItemDeletedEvent $event)
    {
        $this->logger->info('Item deleted event: '. $event->getId());
    }
}
