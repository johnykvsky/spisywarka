<?php

namespace App\Test\Command;

use App\Command\DeleteLoanCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteLoanCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $command = new DeleteLoanCommand($uuid);
        $this->assertSame($uuid->toString(), $command->getId()->toString());
    }
}