<?php
namespace App\DTO;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;
use App\Entity\Enum\UserStatusEnum;

class UserDTO {
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Uuid()
     */
    private $id;
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $firstName;
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $lastName;
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $email;
    /**
     * @var UserStatusEnum
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $status;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $plainPassword;


    /**
     * UserDTO constructor.
     * @param string|null $id
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param UserStatusEnum $status
     * @param string|null $plainPassword
     */
    public function __construct(?string $id,
                                string $firstName,
                                string $lastName,
                                string $email,
                                UserStatusEnum $status,
                                ?string $plainPassword)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->status = $status;
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return ?UuidInterface
     */
    public function getId(): ?UuidInterface
    {
        if (!empty($this->id)) {
            return Uuid::fromString($this->id);
        }

        return null;
    }

    /**
     * @param string|null $id
    */
    public function setId(?string $id): void
    {
        $this->id = $id;
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
     * @return UserStatusEnum
     */
    public function getStatus(): UserStatusEnum
    {
        return $this->status;
    }

    /**
     * @return ?string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
}