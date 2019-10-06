<?php

namespace App\Request;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class UpdateCollectionRequest
{
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
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
     * @var ?string
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $description;

    /**
     * UpdateCollectionRequest constructor.
     * @param string $id
     * @param string $name
     * @param ?string $description
     */
    public function __construct(string $id,
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
        return Uuid::fromString($this->id);
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
