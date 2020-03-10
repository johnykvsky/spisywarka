<?php

namespace App\Test\Command;

use App\Command\ResetPasswordConfirmationCommand;
use PHPUnit\Framework\TestCase;
use Faker\Factory;

class ResetPasswordConfirmationCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $token = $faker->password();
        $password = $faker->password();
        $command = new ResetPasswordConfirmationCommand($token, $password);
        $this->assertSame($token, $command->getToken());
        $this->assertSame($password, $command->getPassword());
    }
}