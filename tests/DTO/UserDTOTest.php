<?php

namespace App\Test\Command;

use App\DTO\UserDTO;
use PHPUnit\Framework\TestCase;
use Faker\Factory;
use Ramsey\Uuid\Uuid;
use App\Entity\Enum\UserStatusEnum;

class UserDTOTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $faker = Factory::create('en_GB');
        $userId = Uuid::uuid4();
        $firstName = $faker->firstName();
        $lastName = $faker->lastName();
        $email = $faker->email();
        $plainPassword = $faker->password();

        $dto = new UserDTO($userId, $firstName, $lastName, $email, UserStatusEnum::active(), $plainPassword);

        $this->assertSame($firstName, $dto->getFirstName());
        $this->assertSame($lastName, $dto->getLastName());
        $this->assertSame($email, $dto->getEmail());
        $this->assertSame($plainPassword, $dto->getPlainPassword());
        $this->assertSame(UserStatusEnum::active()->getValue(), $dto->getStatus()->getValue());
    }
}