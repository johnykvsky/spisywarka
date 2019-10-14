<?php

namespace App\Test\Command;

use App\Command\RegisterUserCommand;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Faker\Factory;

class RegisterUserCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $id = Uuid::uuid4();
        $firstName = $faker->firstName();
        $lastName = $faker->lastName();
        $email = $faker->email();
        $password = $faker->password();

        $command = new RegisterUserCommand($id,  $firstName, $lastName, $email, $password);

        $this->assertSame($id->toString(), $command->getId()->toString());
        $this->assertSame($firstName, $command->getFirstName());
        $this->assertSame($lastName, $command->getLastName());
        $this->assertSame($email, $command->getEmail());
        $this->assertSame($password, $command->getPassword());
    }
}