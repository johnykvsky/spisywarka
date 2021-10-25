<?php

namespace App\Tests\Mothers;

use App\Entity\Item;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

final class ItemMother
{
    /**
     * @return Item
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function random(): Item
    {
        $category = CategoryMother::random();
        $faker = Factory::create('en_GB');
        $user = UserMother::random();
        return new Item(
            Uuid::uuid4(),
            $faker->title(),
            $category,
            (int) $faker->year(),
            $faker->title(255),
            $faker->name(),
            $faker->company(),
            $faker->text(255),
            $faker->company(),
            $faker->url(),
            $user
        );
    }
}