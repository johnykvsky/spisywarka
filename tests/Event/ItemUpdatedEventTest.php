<?php

namespace App\Tests\Event;

use App\Event\ItemUpdatedEvent;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ItemUpdatedEventTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $event = new ItemUpdatedEvent($uuid->toString());
        $this->assertSame($uuid->toString(), $event->getId());
    }
}