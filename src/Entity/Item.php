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
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Table(name="item")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class Item implements \JsonSerializable
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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var ?int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $format;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $publisher;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $store;

    /**
     * @var ?string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var ?\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /** 
     * @ORM\OneToMany(targetEntity="App\Entity\ItemCategory", mappedBy="item", orphanRemoval=true, cascade={"persist", "remove"}) 
     */
    protected $categories;

    /** 
     * @ORM\OneToMany(targetEntity="App\Entity\ItemCollection", mappedBy="item", orphanRemoval=true, cascade={"persist", "remove"}) 
     */
    protected $collections;

    /** 
     * @ORM\OneToOne(targetEntity="App\Entity\Loan", mappedBy="item") 
     */
    protected $loaned;
    
    /**
     * @param UuidInterface $id
     * @param string $name
     * @param ?int $year
     * @param ?string $format
     * @param ?string $author
     * @param ?string $publisher
     * @param ?string $description
     * @param ?string $store
     * @param ?string $url
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        UuidInterface $id,
        string $name,
        ?int $year,
        ?string $format,
        ?string $author,
        ?string $publisher,
        ?string $description,
        ?string $store,
        ?string $url
        )
    {
        $this->setId($id);
        $this->setName($name);
        $this->setYear($year);
        $this->setFormat($format);
        $this->setAuthor($author);
        $this->setPublisher($publisher);
        $this->setDescription($description);
        $this->setStore($store);
        $this->setUrl($url);
        $this->categories = new ArrayCollection();
        $this->collections = new ArrayCollection();
    }
    
    /**
     * @param UuidInterface $id
     * @return Item
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
     * @return Item
     */
    public function setName(string $name): self
    {
        Assertion::notEmpty($name, 'Item name is required');
        Assertion::maxLength($name, 255, 'Item name must less than 255 characters');
        $this->name = $name;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param ?int $year
     * @return Item
     */
    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param ?string $format
     * @return Item
     */
    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param ?string $author
     * @return Item
     */
    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    /**
     * @param ?string $publisher
     * @return Item
     */
    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param ?string $description
     * @return Item
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getStore(): ?string
    {
        return $this->store;
    }

    /**
     * @param ?string $store
     * @return Item
     */
    public function setStore(?string $store): self
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param ?string $url
     * @return Item
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getCollections()
    {
        return $this->collections;
    }
    
    /*
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    /**
     * @return Loan[]
     */
    public function getLoaned()
    {
        return $this->loaned;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function isInCategory(Category $category): bool
    {
        return $this->getCategories()->exists(function($key, $element) use ($category){
            return $category->getId()->equals($element->getCategory()->getId());
        });
    }

    public function addCategory(ItemCategory $itemCategory): void
    {
        $this->categories->add($itemCategory);
    }
    
    public function addCollection(ItemCollection $itemCollection): void
    {
        $this->collections->add($itemCollection);
    }

    /**
     * @param Collection $collection
     * @return bool
     */
    public function isInCollection(Collection $collection): bool
    {
        return $this->getCollections()->exists(function($key, $element) use ($collection){
            return $collection->getId()->equals($element->getCollection()->getId());
        });
    }
    
    /**
     * 
     * @return array
     */
    public function getItemCategories(): array
    {
        $categories = [];
        foreach ($this->getCategories() as $itemCategory) {
            $categories[] = $itemCategory->getCategory();
        }
        return $categories;
    }

    
    /**
     *
     * @return array
     */
    public function getItemCollections(): array
    {
        $collections = [];
        foreach ($this->getCollections() as $itemCollection) {
            $collections[] = $itemCollection->getCollection();
        }
        return $collections;
    }
    
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'year' => $this->getYear(),
            'format' => $this->getFormat(),
            'author' => $this->getAuthor(),
            'publisher' => $this->getPublisher(),
            'description' => $this->getDescription(),
            'store' => $this->getStore(),
            'url' => $this->getUrl(),
            'collections' => $this->getItemCollections(),
            'categories' => $this->getItemCategories(),
            'loaned' => $this->getLoaned(),
            'slug' => $this->getSlug(),
        ];
    }
}
