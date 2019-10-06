<?php

namespace App\Tests\Entity;

use App\Tests\Mothers\CategoryMother;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_name_must_be_less_than_255_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $category = CategoryMother::random();
        $category->setName(\str_repeat('.', 256));
    }
}