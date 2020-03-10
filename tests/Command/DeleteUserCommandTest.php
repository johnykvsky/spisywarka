<?php

namespace App\Test\Command;

use App\Command\DeleteUserCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteUserCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $command = new DeleteUserCommand($uuid);
        $this->assertSame($uuid->toString(), $command->getId()->toString());
    }
}