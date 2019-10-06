<?php

namespace App\Test\Command;

use App\Command\RemoveItemFromCategoryCommand;
use App\Tests\Mothers\LoanMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RemoveItemFromCategoryCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $itemId = Uuid::uuid4();
        $categoryId = Uuid::uuid4();
        $command = new RemoveItemFromCategoryCommand(
            $itemId, $categoryId);
        $this->assertSame($itemId->toString(), $command->getItemId()->toString());
        $this->assertSame($categoryId->toString(), $command->getCategoryId()->toString());
    }
}