<?php

namespace App\Test\Command;

use App\Command\AddItemToCollectionCommand;
use App\Tests\Mothers\LoanMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AddItemToCollectionCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $itemId = Uuid::uuid4();
        $collectionId = Uuid::uuid4();
        $command = new AddItemToCollectionCommand(
            $itemId, $collectionId);
        $this->assertSame($itemId->toString(), $command->getItemId()->toString());
        $this->assertSame($collectionId->toString(), $command->getCollectionId()->toString());
    }
}