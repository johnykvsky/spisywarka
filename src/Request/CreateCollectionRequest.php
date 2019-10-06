<?php

namespace App\Request;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class CreateCollectionRequest
{
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
     * @param string $name
     * @param ?string $description
     */
    public function __construct(string $name, ?string $description)
    {
        $this->name = $name;
        $this->description = $description;
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
