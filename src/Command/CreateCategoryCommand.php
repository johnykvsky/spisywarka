<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class CreateCategoryCommand implements CommandInterface
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
     * @var ?string
     */
    private $description;
    /**
     * @var ?UuidInterface
     */
    private $userId;

    /**
     * @param UuidInterface $id
     * @param string $name
     * @param ?string $description
     * @param ?UuidInterface $userId
     */
    public function __construct(
        UuidInterface $id,
        string $name,
        ?string $description,
        ?UuidInterface $userId
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->userId = $userId;
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
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return ?UuidInterface
     */
    public function getUserId(): ?UuidInterface
    {
        return $this->userId;
    }
}
