<?php

namespace App\CommandHandler;

use App\Command\ResetPasswordConfirmationCommand;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use App\CommandHandler\Exception\ResetPasswordConfirmationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use App\Repository\Exception\UserNotFoundException;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param MessageBusInterface $eventBus
     * @param UserRepository $repository
     * @param LoggerInterface $logger
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     */
    public function __construct(
        MessageBusInterface $eventBus, 
        UserRepository $repository, 
        LoggerInterface $logger,
        UserPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    )
    {
        $this->eventBus = $eventBus;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->encoder = $encoder;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
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
            $token = new UsernamePasswordToken($user, $password, 'main');
            $this->tokenStorage->setToken($token);
            $this->session->set('_security_main', serialize($token));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new ResetPasswordConfirmationException('Pasword reset confirmation error: '.$e->getMessage());
        }

    }

}
