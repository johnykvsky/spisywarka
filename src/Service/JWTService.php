<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Ramsey\Uuid\UuidInterface;
use App\Entity\User;
use App\Security\ValueObject\TokenPayload;

class JWTService
{
    /** @var string */
    private $privateKey;
    /** @var string */
    private $algorithm;

    /**
     * JWTService constructor.
     * @param string $privateKey
     * @param string $algorithm
     */
    public function __construct(string $privateKey, string $algorithm)
    {
        $this->privateKey = $privateKey;
        $this->algorithm = $algorithm;
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateToken(User $user): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 1800;  // token is valid for 30 minutes from the issue time
        $tokenPayload = new TokenPayload(
            $issuedAt,
            $expirationTime,
            $user->getId()->toString(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getStatus()->getValue(),
            $user->getRoles()
        );

        return JWT::encode($tokenPayload->jsonSerialize(), $this->privateKey, $this->algorithm);
    }

    /**
     * @param string $token
     * @return TokenPayload
     */
    public function decode(string $token): TokenPayload
    {
        $payload = (array) JWT::decode($token, $this->privateKey, [$this->algorithm]);
        return new TokenPayload(
            $payload['iat'],
            $payload['exp'],
            $payload['id'],
            $payload['email'],
            $payload['firstName'],
            $payload['lastName'],
            $payload['status'],
            $payload['roles']
        ); 
    }
}
