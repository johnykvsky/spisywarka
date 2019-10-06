<?php

namespace App\Test\Command;

use App\Command\AddItemToCategoryCommand;
use App\Tests\Mothers\LoanMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AddItemToCategoryCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $itemId = Uuid::uuid4();
        $categoryId = Uuid::uuid4();
        $command = new AddItemToCategoryCommand(
            $itemId, $categoryId);
        $this->assertSame($itemId->toString(), $command->getItemId()->toString());
        $this->assertSame($categoryId->toString(), $command->getCategoryId()->toString());
    }
}