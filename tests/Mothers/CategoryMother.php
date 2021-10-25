<?php

namespace App\Tests\Mothers;

use App\Entity\Category;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

final class CategoryMother
{
    /**
     * @return Category
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function random(): Category
    {
        $user = UserMother::random();
        $faker = Factory::create('en_GB');
        return new Category(
            Uuid::uuid4(),
            $faker->title(),
            $faker->text(255),
            $user
        );
    }
}