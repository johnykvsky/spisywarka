<?php

namespace App\Repository;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\ItemCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Repository\Exception\ItemCollectionNotFoundException;

/**
 * @method ItemCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemCollection[]    findAll()
 * @method ItemCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemCollectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ItemCollection::class);
    }

    /**
     * @param Item $item
     * @param Collection $collection
     * @throws ItemCollectionNotFoundException
     * @return itemCollection
     */
    public function getItemCollection(Item $item, Collection $collection): itemCollection
    {
        if ($itemCollection = $this->findOneBy(['item'=> $item, 'collection'=>$collection])) {
            return $itemCollection;
        }

        throw new ItemCollectionNotFoundException('itemCollection not found');
    }
    
    /**
     * @param ItemCollection $itemCollection
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(ItemCollection $itemCollection): void
    {
        $em = $this->getEntityManager();
        $em->persist($itemCollection);
        $em->flush();
    }
    
    /**
     * @param ItemCollection $itemCollection
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(ItemCollection $itemCollection): void
    {
        $em = $this->getEntityManager();
        $em->remove($itemCollection);
        $em->flush();
    }
    
    /**
     * @param Item $item
     * @param Collection $collection
     * @return \App\Entity\ItemCollection|NULL
     */
    public function findItemCollection(Item $item, Collection $collection): ?ItemCollection
    {
        return $this->findOneBy(['item' => $item, 'collection' => $collection]);
    }
}
