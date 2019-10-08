<?php
namespace App\DTO;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class UserRegistrationDTO {
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $firstName;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $lastName;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $email;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $plainPassword;


    /**
     * UserRegistrationDTO constructor.
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $email
     * @param string|null $plainPassword
     */
    public function __construct(?string $firstName = null,
                                ?string $lastName = null,
                                ?string $email = null,
                                ?string $plainPassword = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
}