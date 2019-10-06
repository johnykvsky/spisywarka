<?php
namespace App\DTO;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class CollectionDTO {
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
     * @var string|null
     * @Type("string")
     * @Assert\Length(max=255)
     */
    private $description;

    /**
     * CollectionDTO constructor.
     * @param string|null $id
     * @param string $name
     * @param string|null $description
     */
    public function __construct(?string $id,
                                string $name,
                                ?string $description)
    {

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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