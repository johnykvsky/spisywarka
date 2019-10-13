<?php
namespace App\Security\ValueObject;

class TokenPayload implements \JsonSerializable
{
    /** @var int */
    private $iat;
    /** @var int */
    private $exp;
    /** @var string */
    private $id;
    /** @var string */
    private $email;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $status;
    /** @var array */
    private $roles;

    /**
     * TokenPayload constructor.
     * @param int $iat
     * @param int $exp
     * @param string $id
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $status
     * @param array $roles
     */
    public function __construct(
        int $iat,
        int $exp,
        string $id,
        string $email,
        string $firstName,
        string $lastName,
        string $status,
        array $roles
    )
    {
        $this->iat = $iat;
        $this->exp = $exp;
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->status = $status;
        $this->roles = $roles;
    }

    /**
     * @return int
     */
    public function getIat(): int
    {
        return $this->iat;
    }

    /**
     * @return int
     */
    public function getExp(): int
    {
        return $this->exp;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'iat' => $this->getIat(),
            'exp' => $this->getExp(),
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'status' => $this->getStatus(),
            'roles' => $this->getRoles(),
        ];
    }
}
