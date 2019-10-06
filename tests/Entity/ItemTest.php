<?php

namespace App\Tests\Entity;

use App\Tests\Mothers\ItemMother;
use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_name_must_be_less_than_255_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $item = ItemMother::random();
        $item->setName(\str_repeat('.', 256));
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function test_year_must_be_an_integer(): void
    {
        $this->expectException(\TypeError::class);
        $item = ItemMother::random();
        $item->setYear('test');
    }
}