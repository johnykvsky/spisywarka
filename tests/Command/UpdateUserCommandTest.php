<?php

namespace App\Test\Command;

use App\Command\UpdateUserCommand;
use App\Tests\Mothers\UserMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use App\Entity\Enum\UserStatusEnum;

class UpdateUserCommandTest extends TestCase
{
    /**
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public function test_construct(): void
    {
        $user = UserMother::random();
        $command = new UpdateUserCommand(
            $user->getId(), $user->getFirstName(), $user->getLastName(), $user->getEmail(), $user->getStatus(), ''
        );
        $this->assertSame($user->getId()->toString(), $command->getId()->toString());
        $this->assertSame($user->getFirstName(), $command->getFirstName());
        $this->assertSame($user->getLastName(), $command->getLastName());
        $this->assertSame($user->getEmail(), $command->getEmail());
        $this->assertSame($user->getStatus()->getValue(), $command->getStatus()->getValue());

    }
}