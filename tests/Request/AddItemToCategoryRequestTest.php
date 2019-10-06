<?php

namespace App\Test\Request;

use App\Request\AddItemToCategoryRequest;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AddItemToCategoryRequestTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $itemId = Uuid::uuid4();
        $categoryId = Uuid::uuid4();
        $command = new AddItemToCategoryRequest(
            $itemId->toString(), $categoryId->toString());
        $this->assertSame($itemId->toString(), $command->getItemId()->toString());
        $this->assertSame($categoryId->toString(), $command->getCategoryId()->toString());
    }
}