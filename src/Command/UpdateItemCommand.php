<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class UpdateItemCommand implements CommandInterface
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var UuidInterface
     */
    private $categoryId;
    /**
     * @var ?int
     */
    private $year;
    /**
     * @var ?string
     */
    private $format;
    /**
     * @var ?string
     */
    private $author;
    /**
     * @var ?string
     */
    private $publisher;
    /**
     * @var ?string
     */
    private $description;
    /**
     * @var ?string
     */
    private $store;
    /**
     * @var ?string
     */
    private $url;
    /**
     * @var ?array
     */
    private $collections;

    /**
     * @param UuidInterface $id
     * @param string $name
     * @param UuidInterface $categoryId
     * @param ?int $year
     * @param ?string $format
     * @param ?string $author
     * @param ?string $publisher
     * @param ?string $description
     * @param ?string $store
     * @param ?string $url
     * @param ?array $collections
     */
    public function __construct(UuidInterface $id,
                                string $name,
                                UuidInterface $categoryId,
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
        $this->categoryId = $categoryId;
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
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return UuidInterface
     */
    public function getCategoryId(): UuidInterface
    {
        return $this->categoryId;
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
