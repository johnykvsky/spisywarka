<?php

namespace App\Tests\Mothers;

use App\Entity\ItemCollection;
use App\Entity\Item;
use App\Entity\Collection;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use App\Tests\Mothers\ItemMother;
use App\Tests\Mothers\CategoryMother;

final class ItemCollectionMother
{
    /**
     * @return ItemCollection
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function random(): ItemCollection
    {
        $item = ItemMother::random();
        $collection = CollectionMother::random();

        return new ItemCollection(
            $item,
            $collection
        );
    }

    /**
     * @return ItemCollection
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function given(Item $item, Collection $collection): ItemCollection
    {
        return new ItemCollection(
            $item,
            $collection
        );
    }
}