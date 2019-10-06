<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Repository\Exception\ItemNotFoundException;
use Doctrine\ORM\Query;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @param Item $item
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Item $item): void
    {
        $em = $this->getEntityManager();
        $em->persist($item);
        $em->flush();
    }
    
    /**
     * @param Item $item
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Item $item): void
    {
        $em = $this->getEntityManager();
        $em->remove($item);
        $em->flush();
    }

    /**
     * @param UuidInterface $itemId
     * @throws ItemNotFoundException
     * @return Item
     */
    public function getItem(UuidInterface $itemId): Item
    {
        if ($item = $this->find($itemId)) {
            return $item;
        }

        throw new ItemNotFoundException('Item not found');
    }

    /**
     * @param string|null $searchQuery
     * @return Query
     */
    public function listAllItems(?string $searchQuery = ''): Query
    {
        if (empty($searchQuery)) {
            return $this->createQueryBuilder('i')->getQuery();
        }

        $qb = $this->createQueryBuilder('i');
        return $qb->where($qb->expr()->like('i.name', ':searchQuery'))
        ->orWhere($qb->expr()->like('i.author', ':searchQuery'))
        ->orWhere($qb->expr()->like('i.description', ':searchQuery'))
        ->setParameters([
            'searchQuery' => "%{$searchQuery}%",
        ])
        ->getQuery();
    }
    
    /**
     * @return array
     */
    public function getItemsInCategory($categoryId): array
    {
        return $this->createQueryBuilder('i')
        ->innerJoin('i.categories', 'c')
        ->where('c.category = :categoryId')
        ->setParameters([
            'categoryId' => $categoryId,
        ])
        ->getQuery()
        ->execute();
    }
    
    /**
     * @return array
     */
    public function getItemsInCollection($collectionId): array
    {
        return $this->createQueryBuilder('i')
        ->innerJoin('i.collections', 'c')
        ->where('c.collection = :collectionId')
        ->setParameters([
            'collectionId' => $collectionId,
        ])
        ->getQuery()
        ->execute();
    }

    /**
     * @param string|null $searchQuery
     * @return array
     */
    public function autocompleteItems(?string $searchQuery): array
    {
        $qb = $this->createQueryBuilder('i');
        return $qb->where($qb->expr()->like('i.name', ':searchQuery'))
        ->orWhere($qb->expr()->like('i.author', ':searchQuery'))
        ->orWhere($qb->expr()->like('i.description', ':searchQuery'))
        ->setParameters([
            'searchQuery' => "%{$searchQuery}%",
        ])
        ->getQuery()->getResult();
    }
}
