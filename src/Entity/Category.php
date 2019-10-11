<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Assert\Assertion;
use Assert\AssertionFailedException;
use App\Traits\HasTimestamps;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Swagger\Annotations as SWG;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Table(name="category")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class Category implements \JsonSerializable
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
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="category")
     */
    protected $items;
    
    /**
     * @param UuidInterface $id
     * @param string $name
     * @param ?string $description
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        UuidInterface $id,
        string $name,
        ?string $description
        )
    {
        $this->setId($id);
        $this->setName($name);
        $this->setDescription($description);
    }

    /**
     * @param UuidInterface $id
     * @return Category
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName(string $name): self
    {
        Assertion::notEmpty($name, 'Category name is required');
        Assertion::maxLength($name, 255, 'Category name must less than 255 characters');
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
     * @return Category
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    
    public function getItems(): ?Collection
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'description' => $this->getDescription()
        ];
    }
}
