<?php

namespace App\Test\Command;

use App\Command\DeleteCategoryCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteCategoryCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $uuid = Uuid::uuid4();
        $command = new DeleteCategoryCommand($uuid);
        $this->assertSame($uuid->toString(), $command->getId()->toString());
    }
}