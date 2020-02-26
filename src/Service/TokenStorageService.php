<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;

class TokenStorageService
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /** @var SessionInterface */
    private $session;

    /**
     * TokenStorageService constructor.
     * @param string $tokenStorage
     * @param string $session
     */
    public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }


    /**
     * @param User $user
     */
    private function storeToken(User $user): void
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main');
        $this->tokenStorage->setToken($token);
        $this->session->set('_security_main', serialize($token));
    }
}
