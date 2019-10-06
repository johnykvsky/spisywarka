<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Item;
use App\Entity\ItemCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Repository\Exception\ItemCategoryNotFoundException;

/**
 * @method ItemCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemCategory[]    findAll()
 * @method ItemCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ItemCategory::class);
    }
    
    /**
     * @param Item $item
     * @param Category $category
     * @throws ItemCategoryNotFoundException
     * @return ItemCategory
     */
    public function getItemCategory(Item $item, Category $category): ItemCategory
    {
        if ($itemCategory = $this->findOneBy(['item'=> $item, 'category'=>$category])) {
            return $itemCategory;
        }

        throw new ItemCategoryNotFoundException('ItemCategory not found');
    }

    /**
     * @param ItemCategory $itemCategory
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(ItemCategory $itemCategory): void
    {
        $em = $this->getEntityManager();
        $em->persist($itemCategory);
        $em->flush();
    }
    
    /**
     * @param ItemCategory $itemCategory
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(ItemCategory $itemCategory): void
    {
        $em = $this->getEntityManager();
        $em->remove($itemCategory);
        $em->flush();
    }
    
    /**
     * @param Item $item
     * @param Category $category
     * @return \App\Entity\ItemCategory|NULL
     */
    public function findItemCategory(Item $item, Category $category): ?ItemCategory
    {
        return $this->findOneBy(['item' => $item, 'category' => $category]);
    }
}
