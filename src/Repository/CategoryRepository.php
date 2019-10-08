<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Ramsey\Uuid\UuidInterface;
use App\Repository\Exception\CategoryNotFoundException;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }
    
    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Category $category): void
    {
        $em = $this->getEntityManager();
        $em->persist($category);
        $em->flush();
    }
    
    /**
     * @param Category $category
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Category $category): void
    {
        $em = $this->getEntityManager();
        $em->remove($category);
        $em->flush();
    }
    
    /**
     * @param UuidInterface $categoryId
     * @throws CategoryNotFoundException
     * @return Category
     */
    public function getCategory(UuidInterface $categoryId): Category
    {
        if ($category = $this->find($categoryId)) {
            return $category;
        }
        
        throw new CategoryNotFoundException('Category not found');
    }
    
    /**
     * @return array
     */
    public function listAllCategories(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
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
        ->add('orderBy', 'c.name ASC')
        ->setParameters([
            'searchQuery' => "%{$searchQuery}%",
        ])
        ->getQuery()->getResult();
    }
}
