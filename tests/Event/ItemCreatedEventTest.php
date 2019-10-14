<?php

namespace App\Tests\Event;

use App\Event\ItemCreatedEvent;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ItemCreatedEventTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $event = new ItemCreatedEvent($uuid->toString());
        $this->assertSame($uuid->toString(), $event->getId());
    }
}