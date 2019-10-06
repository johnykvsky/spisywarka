<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Traits\HasTimestamps;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity("email")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class User implements UserInterface
{
    use HasTimestamps;

    /**
     * @var UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     */
    protected $password;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $passwordRequestToken;

    /**
     * @var array $roles
     *
     * @ORM\Column(type="array")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var ?\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * This is never stored in DB, only used in form
     * 
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @param UuidInterface $id
     */
    public function __construct(UuidInterface $id)
    {
        $this->setId($id);
    }

    /**
     * @param UuidInterface $id
     * @return User
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        
        return $this;
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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }


    /**
     * @return null|string
     */
    public function getPasswordRequestToken(): ?string
    {
        return $this->passwordRequestToken;
    }

    /**
     * @param null|string $passwordRequestToken
     *
     * @return User
     */
    public function setPasswordRequestToken(?string $passwordRequestToken): User
    {
        $this->passwordRequestToken = $passwordRequestToken;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * This is never stored in DB, only used in form
     *
     * @return null|string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * This is never stored in DB, only used in form
     * 
     * @param null|string $plainPassword
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
}