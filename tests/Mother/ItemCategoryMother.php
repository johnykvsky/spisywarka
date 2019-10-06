<?php

namespace App\Tests\Mothers;

use App\Entity\ItemCategory;
use App\Entity\Item;
use App\Entity\Category;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use App\Tests\Mothers\ItemMother;
use App\Tests\Mothers\CategoryMother;

final class ItemCategoryMother
{
    /**
     * @return ItemCategory
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function random(): ItemCategory
    {
        $item = ItemMother::random();
        $category = CategoryMother::random();

        return new ItemCategory(
            $item,
            $category
        );
    }

    /**
     * @return ItemCategory
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function given(Item $item, Category $category): ItemCategory
    {
        return new ItemCategory(
            $item,
            $category
        );
    }
}