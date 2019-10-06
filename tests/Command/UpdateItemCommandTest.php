<?php

namespace App\Test\Command;

use App\Command\UpdateItemCommand;
use App\Tests\Mothers\ItemMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateItemCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $item = ItemMother::random()->jsonSerialize();
        $command = new UpdateItemCommand(
            Uuid::fromString($item['id']), $item['name'], $item['year'], $item['format'], $item['author'],
            $item['publisher'], $item['description'], $item['store'], $item['url'], null, null
            );
        $this->assertSame($item['id'], (string) $command->getId());
        $this->assertSame($item['name'], $command->getName());
        $this->assertSame($item['year'], $command->getYear());
        $this->assertSame($item['format'], $command->getFormat());
        $this->assertSame($item['author'], $command->getAuthor());
        $this->assertSame($item['publisher'], $command->getPublisher());
        $this->assertSame($item['description'], $command->getDescription());
        $this->assertSame($item['store'], $command->getStore());
        $this->assertSame($item['url'], $command->getUrl());
    }
}