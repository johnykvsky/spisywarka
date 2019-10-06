<?php

namespace App\Request;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Type;

class AddItemToCategoryRequest
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
    private $categoryId;

    /**
     * AddItemToCategoryRequest constructor.
     * @param string $itemId
     * @param string $categoryId
     */
    public function __construct(string $itemId, string $categoryId)
    {
        $this->itemId = $itemId;
        $this->categoryId = $categoryId;
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
    public function getCategoryId(): UuidInterface
    {
        return Uuid::fromString($this->categoryId);
    }
}
