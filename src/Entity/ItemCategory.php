<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Assert\Assertion;
use Assert\AssertionFailedException;
use App\Traits\HasTimestamps;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
//use App\Entity\Item;
//use App\Entity\Category;
use Swagger\Annotations as SWG;

/**
 * @ORM\Table(name="item_categories")
 * @ORM\Entity(repositoryClass="App\Repository\ItemCategoryRepository")
 */
class ItemCategory
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Item", inversedBy="categories", cascade={"persist"})
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", nullable=false)
     */
    private $item;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    /**
     * @param Item $item
     * @param Category $category
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(Item $item, Category $category)
    {
        $this->setItem($item);
        $this->setCategory($category);
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
     * @return ItemCategory
     */
    public function setItem(Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return ItemCategory
     */
    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
