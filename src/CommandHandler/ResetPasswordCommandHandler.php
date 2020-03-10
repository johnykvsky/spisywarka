<?php

namespace App\CommandHandler;

use App\Command\ResetPasswordCommand;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\PasswordResetException;
use App\Repository\Exception\UserNotFoundException;
use Psr\Log\LoggerInterface;
use App\Entity\User;

class ResetPasswordCommandHandler implements CommandHandlerInterface
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
     * @param MessageBusInterface $eventBus
     * @param UserRepository $repository
     * @param LoggerInterface $logger
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        UserRepository $repository, 
        LoggerInterface $logger
    )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param ResetPasswordCommand $command
     */
    public function __invoke(ResetPasswordCommand $command)
    {
        try {
            $email = filter_var($command->getEmail(), FILTER_SANITIZE_EMAIL);
            $token = bin2hex(random_bytes(32));
            $user = $this->repository->findOneBy(['email' => $email]);
            
            if (!$user instanceof User) {
                throw new UserNotFoundException('User not found');
            }

            $user->setPasswordRequestToken($token);
            $this->repository->save($user);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new PasswordResetException('Pasword reset error: '.$e->getMessage());
        }

    }

}
