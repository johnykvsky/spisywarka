<?php

namespace App\Tests\EventHandler;

use App\Event\ItemCreatedEvent;
use App\EventHandler\ItemCreatedEventHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBus;
use Ramsey\Uuid\Uuid;
use Psr\Log\LoggerInterface;

class ItemCreatedEventHandlerTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_item_created_event(): void
    {
        $command = new ItemCreatedEvent(Uuid::uuid4());
        $eventBus = new MessageBus();
        $logger = $this->createMock(LoggerInterface::class);
        $handler = new ItemCreatedEventHandler($eventBus, $logger);
        $result = $handler($command);
        $this->assertNull($result);
    }
}