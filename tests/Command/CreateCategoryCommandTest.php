<?php

namespace App\Test\Command;

use App\Command\CreateCategoryCommand;
use App\Tests\Mothers\CategoryMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateCategoryCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $category = CategoryMother::random()->jsonSerialize();
        $command = new CreateCategoryCommand(
            Uuid::fromString($category['id']), $category['name'], $category['description'], Uuid::uuid4()
        );
        $this->assertSame($category['id'], (string) $command->getId());
        $this->assertSame($category['name'], $command->getName());
        $this->assertSame($category['description'], $command->getDescription());
    }
}