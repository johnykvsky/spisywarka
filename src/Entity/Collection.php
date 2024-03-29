<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Assert\Assertion;
use Assert\AssertionFailedException;
use App\Traits\HasTimestamps;
use Doctrine\ORM\PersistentCollection;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="collection")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\CollectionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class Collection implements \JsonSerializable, UserAwareInterface
{
    use HasTimestamps;
    
    /**
     * @var UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     * @SWG\Property(description="UUID", type="string", readOnly=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ItemCollection", mappedBy="collection")
     */
    protected $items;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="collections")
     */
    private $user;

    /**
     * @param UuidInterface $id
     * @param string $name
     * @param ?string $description
     * @param User $user
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        UuidInterface $id,
        string $name,
        ?string $description,
        User $user
        )
    {
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
        $this->setUser($user);
    }
    
    /**
     * @param UuidInterface $id
     * @return Collection
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
     * @param User $user
     * @return Category
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     * @return Collection
     */
    public function setName(string $name): self
    {
        Assertion::notEmpty($name, 'Collection name is required');
        Assertion::maxLength($name, 255, 'Collection name must less than 255 characters');
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    /**
     * @param ?string $description
     * @return Collection
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function getItems(): ?PersistentCollection
    {
        return $this->items;
    }
    
    /**
     * @return bool
     */
    public function hasItems(): bool
    {
        return !($this->getItems() === null || $this->getItems()->isEmpty());
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
    
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'userId' => $this->getUser()->getId()->toString(),
        ];
    }
}
