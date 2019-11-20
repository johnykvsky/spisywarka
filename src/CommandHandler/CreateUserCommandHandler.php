<?php

namespace App\CommandHandler;

use App\Command\CreateUserCommand;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\UserNotCreatedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class CreateUserCommandHandler implements CommandHandlerInterface
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
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->encoder = $encoder;
    }

    /**
     * @param CreateUserCommand $command
     */
    public function __invoke(CreateUserCommand $command)
    {
        try {
            $user = new User($command->getId());
            $user->setFirstName($command->getFirstName());
            $user->setLastName($command->getLastName());
            $user->setEmail($command->getEmail());
            $user->setStatus($command->getStatus());
            
            $password = $this->encoder->encodePassword($user, $command->getPlainPassword());
            $user->setPassword($password);

            $this->repository->save($user);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new UserNotCreatedException('User was not created: '.$e->getMessage());
        }

    }

}
