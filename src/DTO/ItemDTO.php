<?php
namespace App\DTO;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class ItemDTO {
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
    private $name;
    /**
     * @var string
     * @Type("string")
     * @Assert\Uuid()
     */
    private $category;
    /**
     * @var int|null
     * @Type("int")
     * @Assert\Length(max=255)
     */
    private $year;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $format;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $author;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $publisher;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $description;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $store;
    /**
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $url;
    /**
     * @var array|null
     * @Type("array")
     */
    private $collections;

    /**
     * ItemDTO constructor.
     * @param string|null $id
     * @param string $name
     * @param string $category
     * @param int|null $year
     * @param string|null $format
     * @param string|null $author
     * @param string|null $publisher
     * @param string|null $description
     * @param string|null $store
     * @param string|null $url
     * @param array|null $collections
     */
    public function __construct(?string $id,
                                string $name,
                                string $category,
                                ?int $year,
                                ?string $format,
                                ?string $author,
                                ?string $publisher,
                                ?string $description,
                                ?string $store,
                                ?string $url,
                                ?array $collections)
    {

        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->year = $year;
        $this->format = $format;
        $this->author = $author;
        $this->publisher = $publisher;
        $this->description = $description;
        $this->store = $store;
        $this->url = $url;
        $this->collections = $collections;
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
     * @return ?UuidInterface
     */
    public function getCategory(): ?UuidInterface
    {
        if (!empty($this->category)) {
            return Uuid::fromString($this->category);
        }

        return null;
    }

    /**
     * @param string|null $category
    */
    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return ?int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @return ?string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @return ?string
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @return ?string
     */
    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    /**
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return ?string
     */
    public function getStore(): ?string
    {
        return $this->store;
    }

    /**
     * @return ?string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return ?array
     */
    public function getCollections(): ?array
    {
        return $this->collections;
    }
}