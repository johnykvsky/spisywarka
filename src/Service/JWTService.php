<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Ramsey\Uuid\UuidInterface;

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
     * @param string $userId
     * @param string $email
     * @param array $extraFields
     * @return string
     */
    public function generateToken(string $userId, string $email, array $extraFields = []): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 1800;  // token is valid for 30 minutes from the issue time
        $payload = array(
            'id' => $userId,
            'email' => $email,
            'iat' => $issuedAt,
            'exp' => $expirationTime,
        );
        foreach ($extraFields as $field => $value) {
            if (!array_key_exists($field, $payload)) {
                $payload[$field] = $value;
            }
        }

        return JWT::encode($payload, $this->privateKey, $this->algorithm);
    }

    /**
     * @param string $token
     * @return object
     */
    public function decode(string $token): object
    {
        return JWT::decode($token, $this->privateKey, [$this->algorithm]);
    }
}
