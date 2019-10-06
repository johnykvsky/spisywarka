<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class UpdateCollectionCommand implements CommandInterface
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
     * @param UuidInterface $id
     * @param string $name
     * @param ?string $description
     */
    public function __construct(UuidInterface $id,
                                string $name,
                                ?string $description
                                )
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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
}
