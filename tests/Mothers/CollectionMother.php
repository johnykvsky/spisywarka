<?php

namespace App\Tests\Mothers;

use App\Entity\Collection;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

final class CollectionMother
{
    /**
     * @return Collection
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function random(): Collection
    {
        $faker = Factory::create('en_GB');
        $user = UserMother::random();
        return new Collection(
            Uuid::uuid4(),
            $faker->title(),
            $faker->text(255),
            $user
        );
    }
}