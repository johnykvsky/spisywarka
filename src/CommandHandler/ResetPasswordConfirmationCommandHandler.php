<?php

namespace App\CommandHandler;

use App\Command\ResetPasswordConfirmationCommand;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ResetPasswordConfirmationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Repository\Exception\UserNotFoundException;
use App\Service\TokenStorageService;

class ResetPasswordConfirmationCommandHandler implements CommandHandlerInterface
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
     * @var TokenStorageService
     */
    private $tokenStorageService;

    /**
     * @param MessageBusInterface $eventBus
     * @param UserRepository $repository
     * @param LoggerInterface $logger
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenStorageService $tokenStorage
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        UserRepository $repository, 
        LoggerInterface $logger,
        UserPasswordEncoderInterface $encoder,
        TokenStorageService $tokenStorageService
    )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->encoder = $encoder;
        $this->tokenStorageService = $tokenStorageService;
    }

    /**
     * @param ResetPasswordConfirmationCommand $command
     */
    public function __invoke(ResetPasswordConfirmationCommand $command)
    {
        try {
            $user = $this->repository->findOneBy(['passwordRequestToken' => $command->getToken()]);

            if (!$user instanceof User) {
                throw new UserNotFoundException('User not found');
            }

            $password = $this->encoder->encodePassword($user, $command->getPassword());
            $user->setPassword($password);
            $user->setPasswordRequestToken(null);
            $this->repository->save($user);
            $this->tokenStorageService->storeToken($user);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ResetPasswordConfirmationException('Pasword reset confirmation error: '.$e->getMessage());
        }

    }

}
