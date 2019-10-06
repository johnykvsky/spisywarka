<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class UpdateUserProfileCommand implements CommandInterface
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $firstName;
    /**
     * @var string
     */
    private $lastName;
    /**
     * @var string
     */
    private $email;
    /**
     * @var ?string
     */
    private $plainPassword;
    
    /**
     * @param UuidInterface $id
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param ?string $plainPassword
     */
    public function __construct(
        UuidInterface $id,
        string $firstName,
        string $lastName,
        string $email,
        ?string $plainPassword)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
    }
    
    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
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
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return ?string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
}
