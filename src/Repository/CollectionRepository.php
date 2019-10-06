<?php

namespace App\Repository;

use App\Entity\Collection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Ramsey\Uuid\UuidInterface;
use App\Repository\Exception\CollectionNotFoundException;

/**
 * @method Collection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection[]    findAll()
 * @method Collection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Collection::class);
    }

    /**
     * @param Collection $collection
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Collection $collection): void
    {
        $em = $this->getEntityManager();
        $em->persist($collection);
        $em->flush();
    }
    
    /**
     * @param Collection $collection
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Collection $collection): void
    {
        $em = $this->getEntityManager();
        $em->remove($collection);
        $em->flush();
    }
    
    /**
     * @param UuidInterface $collectionId
     * @throws CollectionNotFoundException
     * @return Collection
     */
    public function getCollection(UuidInterface $collectionId): Collection
    {
        if ($collection = $this->find($collectionId)) {
            return $collection;
        }
        
        throw new CollectionNotFoundException('Collection not found');
    }
    
    /**
     * @return array
     */
    public function listAllCollections(): array
    {
        return $this->findAll();
    }

    /**
     * @param string|null $searchQuery
     * @return array
     */
    public function autocompleteItems(?string $searchQuery): array
    {
        $qb = $this->createQueryBuilder('c');
        return $qb->where($qb->expr()->like('c.name', ':searchQuery'))
        ->orWhere($qb->expr()->like('c.description', ':searchQuery'))
        ->setParameters([
            'searchQuery' => "%{$searchQuery}%",
        ])
        ->getQuery()->getResult();
    }
}
