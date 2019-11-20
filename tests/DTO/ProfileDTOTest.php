<?php

namespace App\Test\Command;

use App\DTO\ProfileDTO;
use PHPUnit\Framework\TestCase;
use Faker\Factory;

class ProfileDTOTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $firstName = $faker->firstName();
        $lastName = $faker->lastName();
        $email = $faker->email();
        $plainPassword = $faker->password();

        $dto = new ProfileDTO($firstName, $lastName, $email, $plainPassword);

        $this->assertSame($firstName, $dto->getFirstName());
        $this->assertSame($lastName, $dto->getLastName());
        $this->assertSame($email, $dto->getEmail());
        $this->assertSame($plainPassword, $dto->getPlainPassword());
    }
}