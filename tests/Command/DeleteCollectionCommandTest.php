<?php

namespace App\Test\Command;

use App\Command\DeleteCollectionCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteCollectionCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $command = new DeleteCollectionCommand($uuid);
        $this->assertSame($uuid->toString(), $command->getId()->toString());
    }
}