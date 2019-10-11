<?php

namespace App\Test\Command;

use App\Command\CreateItemCommand;
use App\Tests\Mothers\ItemMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateItemCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $item = ItemMother::random();
        $command = new CreateItemCommand(
            $item->getId(),
            $item->getName(),
            $item->getCategory()->getId(),
            $item->getYear(), 
            $item->getFormat(), 
            $item->getAuthor(), 
            $item->getPublisher(), 
            $item->getDescription(), 
            $item->getStore(), 
            $item->getUrl(), 
            null
            );
        $this->assertSame($item->getId()->toString(), $command->getId()->toString());
        $this->assertSame($item->getName(), $command->getName());
        $this->assertSame($item->getCategory()->getId()->toString(), $command->getCategoryId()->toString());
        $this->assertSame($item->getYear(), $command->getYear());
        $this->assertSame($item->getFormat(), $command->getFormat());
        $this->assertSame($item->getAuthor(), $command->getAuthor());
        $this->assertSame($item->getPublisher(), $command->getPublisher());
        $this->assertSame($item->getDescription(), $command->getDescription());
        $this->assertSame($item->getStore(), $command->getStore());
        $this->assertSame($item->getUrl(), $command->getUrl());
    }
}