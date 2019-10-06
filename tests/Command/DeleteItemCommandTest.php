<?php

namespace App\Test\Command;

use App\Command\DeleteItemCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteItemCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $command = new DeleteItemCommand($uuid);
        $this->assertSame($uuid->toString(), $command->getId()->toString());
    }
}