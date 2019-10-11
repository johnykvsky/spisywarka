<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Assert\AssertionFailedException;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="item_collections")
 * @ORM\Entity(repositoryClass="App\Repository\ItemCollectionRepository")
 */
class ItemCollection
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Item", inversedBy="collections", cascade={"persist"})
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     */
    private $item;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Collection", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="collection_id", referencedColumnName="id", nullable=false)
     */
    private $collection;

    /**
     * @param Item $item
     * @param Collection $collection
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(Item $item, Collection $collection)
    {
        $this->setItem($item);
        $this->setCollection($collection);
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     * @return ItemCollection
     */
    public function setItem(Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @param Collection $collection
     * @return ItemCollection
     */
    public function setCollection(Collection $collection): self
    {
        $this->collection = $collection;

        return $this;
    }
}
