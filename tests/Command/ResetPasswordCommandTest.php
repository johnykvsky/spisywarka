<?php

namespace App\Test\Command;

use App\Command\ResetPasswordCommand;
use PHPUnit\Framework\TestCase;
use Faker\Factory;

class ResetPasswordCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $email = $faker->email();
        $command = new ResetPasswordCommand($email);
        $this->assertSame($email, $command->getEmail());
    }
}