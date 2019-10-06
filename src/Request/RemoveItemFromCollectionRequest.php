<?php

namespace App\Request;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class RemoveItemFromCollectionRequest
{
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private $itemId;
    /**
     * @var string
     * @Type("string")
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private $collectionId;

    /**
     * RemoveItemFromCollectionRequest constructor.
     * @param string $itemId
     * @param string $collectionId
     */
    public function __construct(string $itemId, string $collectionId)
    {
        $this->itemId = $itemId;
        $this->collectionId = $collectionId;
    }

    /**
     * @return UuidInterface
     */
    public function getItemId(): UuidInterface
    {
        return Uuid::fromString($this->itemId);
    }

    /**
     * @return UuidInterface
     */
    public function getCollectionId(): UuidInterface
    {
        return Uuid::fromString($this->collectionId);
    }
}
