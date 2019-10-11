<?php

namespace App\Request;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class CreateItemRequest
{
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
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private $categoryId;
    /**
     * @var int
     * @Type("int")
     * @Assert\Length(max=255)
     */
    private $year;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $format;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $author;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $publisher;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $description;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $store;
    /**
     * @var string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $url;

    /**
     * CreateItemRequest constructor.
     * @param string $name
     * @param string $categoryId
     * @param int $year
     * @param string $format
     * @param string $author
     * @param string $publisher
     * @param string $description
     * @param string $store
     * @param string $url
     */
    public function __construct(string $name,
                                string $categoryId,
                                int $year,
                                string $format,
                                string $author,
                                string $publisher,
                                string $description,
                                string $store,
                                string $url)
    {
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->year = $year;
        $this->format = $format;
        $this->author = $author;
        $this->publisher = $publisher;
        $this->description = $description;
        $this->store = $store;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return UuidInterface
     */
    public function getCategoryId(): UuidInterface
    {
        return Uuid::fromString($this->categoryId);
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
}
