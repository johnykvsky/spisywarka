<?php

namespace App\CommandHandler;

use App\Command\RegisterUserCommand;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\UserNotRegisteredException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Enum\UserStatusEnum;

class RegisterUserCommandHandler implements CommandHandlerInterface
{
    /**
     * @var UserRepository
     */
    private $repository;    
    /**
     * @var MessageBusInterface
     */
    private $eventBus;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param MessageBusInterface $eventBus
     * @param UserRepository $repository
     * @param LoggerInterface $logger
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        UserRepository $repository, 
        LoggerInterface $logger,
        UserPasswordEncoderInterface $encoder)
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->encoder = $encoder;
    }

    /**
     * @param RegisterUserCommand $command
     */
    public function __invoke(RegisterUserCommand $command)
    {
        try {
            $user = new User($command->getId());

            $password = $this->encoder->encodePassword($user, $command->getPassword());    

            $user->setPassword($password);
            $user->setFirstName($command->getFirstName());
            $user->setLastName($command->getLastName());
            $user->setEmail($command->getEmail());
            $user->setStatus(UserStatusEnum::active());
            $this->repository->save($user);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new UserNotRegisteredException('User was not registered: '.$e->getMessage());
        }
    }
}
