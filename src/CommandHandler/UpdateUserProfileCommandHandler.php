<?php

namespace App\CommandHandler;

use App\Command\UpdateUserProfileCommand;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\UserNotUpdatedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdateUserProfileCommandHandler implements CommandHandlerInterface
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
     * @param UpdateUserProfileCommand $command
     */
    public function __invoke(UpdateUserProfileCommand $command)
    {
        try {
            $user = $this->repository->getUser($command->getId());
            $user->setFirstName($command->getFirstName());
            $user->setLastName($command->getLastName());
            $user->setEmail($command->getEmail());

            if (!empty($command->getPlainPassword())) {
                $password = $this->encoder->encodePassword($user, $command->getPlainPassword());
                $user->setPassword($password);
            }

            $this->repository->save($user);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new UserNotUpdatedException('User was not updated: '.$e->getMessage());
        }

    }

}
