<?php

namespace App\Tests\Mothers;

use App\Entity\User;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

final class UserMother
{
    /**
     * @return User
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function random(): User
    {
        $faker = Factory::create('en_GB');

        $user = new User(Uuid::uuid4());
        $user->setFirstName($faker->firstName());
        $user->setLastName($faker->lastName());
        $user->setEmail($faker->email());
        $user->setPlainPassword($faker->password());
        $user->setPassword($faker->password());
        return $user;
    }
}