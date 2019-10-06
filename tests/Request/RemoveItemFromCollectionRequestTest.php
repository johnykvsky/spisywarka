<?php

namespace App\Test\Request;

use App\Request\RemoveItemFromCollectionRequest;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RemoveItemFromCollectionRequestTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $itemId = Uuid::uuid4();
        $collectionId = Uuid::uuid4();
        $command = new RemoveItemFromCollectionRequest(
            $itemId->toString(), $collectionId->toString());
        $this->assertSame($itemId->toString(), $command->getItemId()->toString());
        $this->assertSame($collectionId->toString(), $command->getCollectionId()->toString());
    }
}