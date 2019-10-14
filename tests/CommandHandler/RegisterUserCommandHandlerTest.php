<?php

namespace App\Tests\CommandHandler;

use App\Command\RegisterUserCommand;
use App\CommandHandler\RegisterUserCommandHandler;
use App\CommandHandler\Exception\UserNotRegisteredException;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Mothers\UserMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterUserCommandHandlerTest extends TestCase
{
    /**
     * @throws UserNotRegisteredException
     * @throws \Assert\AssertionFailedException
     */
    public function test_user_registered(): void
    {
        $userMock = UserMother::random();
        $repository = $this->createMock(UserRepository::class);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    function (User $user) use ($userMock) {
                        self::assertSame($userMock->getId()->toString(), $user->getId()->toString());
                        self::assertSame($userMock->getFirstName(), $user->getFirstName());
                        self::assertSame($userMock->getLastName(), $user->getLastName());
                        self::assertSame($userMock->getEmail(), $user->getEmail());
                        self::assertSame($userMock->getPassword(), $user->getPassword());

                        return true;
                    }
                )
            );

        $command = new RegisterUserCommand(
            $userMock->getId(), $userMock->getFirstName(), $userMock->getLastName(), $userMock->getEmail(), $userMock->getPlainPassword()
        );
        
        $eventBus = $this->createMock(MessageBusInterface::class);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $encoder->expects($this->once())->method('encodePassword')->willReturn($userMock->getPassword());
        //$eventBus->expects(self::once())->method('dispatch')->withAnyParameters();
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $handler = new RegisterUserCommandHandler($eventBus, $repository, $logger, $encoder);
        
        $handler($command);
    }
}