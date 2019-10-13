<?php

namespace App\Security;

use App\Security\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Repository\UserRepository;
use App\Service\JWTService;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /* @var LoggerInterface */
    private $logger;
    /* @var UserRepository */
    private $userRepository;
    /* @var JWTService */
    private $jwtService;

    /**
     * Does the authenticator support the given Request?
     * If this returns false, the authenticator will be skipped.
     *
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param JWTService $jwtService
     */
    public function __construct(LoggerInterface $logger, UserRepository $userRepository, JWTService $jwtService)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array).
     *
     * Whatever value you return here will be passed to getUser() and checkCredentials()
     *
     * @param Request $request
     * @return array
     * @throws \UnexpectedValueException If null is returned
     */
    public function getCredentials(Request $request)
    {
        $apiToken = str_replace('Bearer ', '', $request->headers->get('Authorization') ?? '');

        return array(
            'token' => $apiToken,
        );
    }

    /**
     * Return a UserInterface object based on the credentials.
     * The *credentials* are the return value from getCredentials()
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @throws AuthenticationException
     * @throws UsernameNotFoundException
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiToken = $credentials['token'];

        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('Missing token payload');
        }

        try {
            $payload = $this->jwtService->decode($apiToken);
            $userEntity = $this->userRepository->getUser(Uuid::fromString($payload->getId()));
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException($e->getMessage());
        }

        // if a User object, checkCredentials() is called
        $user = new User();
        $user->setId($userEntity->getId());
        $user->setStatus($userEntity->getStatus()->getValue());
        $user->setEmail($userEntity->getEmail());
        $user->setFirstName($userEntity->getFirstName());
        $user->setLastName($userEntity->getLastName());
        $user->setRoles($userEntity->getRoles());
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = \json_decode($exception->getMessageKey(), true);
        $data = [
            'errors' =>[
                'code' => 'authentication_error',
                'message' => $message['errors']['message'] ?? $exception->getMessageKey()
            ]
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request $request
     * @param AuthenticationException $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritdoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
