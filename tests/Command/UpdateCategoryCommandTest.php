<?php

namespace App\Test\Command;

use App\Command\UpdateCategoryCommand;
use App\Tests\Mothers\CategoryMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateCategoryCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $category = CategoryMother::random()->jsonSerialize();
        $command = new UpdateCategoryCommand(
            Uuid::fromString($category['id']), $category['name'], $category['description']);
        $this->assertSame($category['id'], $command->getId()->toString());
        $this->assertSame($category['name'], $command->getName());
        $this->assertSame($category['description'], $command->getDescription());
    }
}