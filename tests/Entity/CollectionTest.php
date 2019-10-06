<?php

namespace App\Tests\Entity;

use App\Tests\Mothers\CollectionMother;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_name_must_be_less_than_255_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $category = CollectionMother::random();
        $category->setName(\str_repeat('.', 256));
    }
}