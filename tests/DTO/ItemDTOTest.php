<?php

namespace App\Test\Command;

use App\DTO\ItemDTO;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Faker\Factory;
use App\Tests\Mothers\ItemMother;

class ItemDTOTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $itemMother = ItemMother::random();
        $dto = new ItemDTO(
            $itemMother->getId()->toString(),
            $itemMother->getName(),
            $itemMother->getCategory()->getId()->toString(),
            $itemMother->getYear(),
            $itemMother->getFormat(),
            $itemMother->getAuthor(),
            $itemMother->getPublisher(),
            $itemMother->getDescription(),
            $itemMother->getStore(),
            $itemMother->getUrl(),
            []
        );
        $this->assertSame($itemMother->getId()->toString(), $dto->getId()->toString());
        $this->assertSame($itemMother->getName(), $dto->getName());
        $this->assertSame($itemMother->getCategory()->getId()->toString(), $dto->getCategoryId()->toString());
        $this->assertSame($itemMother->getYear(), $dto->getYear());
        $this->assertSame($itemMother->getFormat(), $dto->getFormat());
        $this->assertSame($itemMother->getAuthor(), $dto->getAuthor());
        $this->assertSame($itemMother->getPublisher(), $dto->getPublisher());
        $this->assertSame($itemMother->getDescription(), $dto->getDescription());
        $this->assertSame($itemMother->getStore(), $dto->getStore());
        $this->assertSame($itemMother->getUrl(), $dto->getUrl());
        $this->assertSame([], $dto->getCollections());
    }
}