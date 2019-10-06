<?php

namespace App\Tests\Mothers;

use App\Entity\Loan;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use App\Tests\Mothers\ItemMother;

final class LoanMother
{
    /**
     * @return Loan
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function random(): Loan
    {
        $faker = Factory::create('en_GB');

        $item = ItemMother::random();

        return new Loan(
            Uuid::uuid4(),
            $item,
            $faker->name(),
            null,
            null
        );
    }
}