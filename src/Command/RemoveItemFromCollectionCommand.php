<?php

namespace App\Command;

use Ramsey\Uuid\UuidInterface;

class RemoveItemFromCollectionCommand implements CommandInterface
{
    /**
     * @var UuidInterface
     */
    private $itemId;
    /**
     * @var UuidInterface
     */
    private $collectionId;

    /**
     * @param UuidInterface $itemId
     * @param UuidInterface $collectionId
     */
    public function __construct(UuidInterface $itemId, UuidInterface $collectionId)
    {
        $this->itemId = $itemId;
        $this->collectionId = $collectionId;
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
    public function getCollectionId(): UuidInterface
    {
        return $this->collectionId;
    }
}
