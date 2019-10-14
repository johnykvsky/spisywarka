<?php

namespace App\Test\Command;

use App\DTO\CategoryDTO;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Faker\Factory;

class CategoryDTOTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $id = Uuid::uuid4();
        $name = $faker->title();
        $description = null;
        $dto = new CategoryDTO($id, $name, null);
        $this->assertSame($id->toString(), $dto->getId()->toString());
        $this->assertSame($name, $dto->getName());
        $this->assertSame(null, $dto->getDescription());
    }
}