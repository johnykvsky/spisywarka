<?php

namespace App\Tests\Event;

use App\Event\ItemDeletedEvent;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ItemDeletedEventTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $event = new ItemDeletedEvent($uuid->toString());
        $this->assertSame($uuid->toString(), $event->getId());
    }
}