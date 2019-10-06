<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class RemoveItemFromCategoryCommand implements CommandInterface
{
    /**
     * @var UuidInterface
     */
    private $itemId;
    /**
     * @var UuidInterface
     */
    private $categoryId;

    /**
     * @param UuidInterface $itemId
     * @param UuidInterface $categoryId
     */
    public function __construct(UuidInterface $itemId, UuidInterface $categoryId)
    {
        $this->itemId = $itemId;
        $this->categoryId = $categoryId;
    }

    /**
     * @return UuidInterface
     */
    public function getItemId(): UuidInterface
    {
        return $this->itemId;
    }

    /**
     * @return UuidInterface
     */
    public function getCategoryId(): UuidInterface
    {
        return $this->categoryId;
    }
}
