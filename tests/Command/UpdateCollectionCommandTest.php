<?php

namespace App\Test\Command;

use App\Command\UpdateCollectionCommand;
use App\Tests\Mothers\CollectionMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateCollectionCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $collection = CollectionMother::random()->jsonSerialize();
        $command = new UpdateCollectionCommand(
            Uuid::fromString($collection['id']), $collection['name'], $collection['description']);
        $this->assertSame($collection['id'], (string) $command->getId());
        $this->assertSame($collection['name'], $command->getName());
        $this->assertSame($collection['description'], $command->getDescription());
    }
}