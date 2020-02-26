<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Repository\Exception\LoanNotFoundException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Query;

/**
 * @method Loan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loan[]    findAll()
 * @method Loan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoanRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    /**
     * @param Loan $loan
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Loan $loan): void
    {
        $em = $this->getEntityManager();
        $em->persist($loan);
        $em->flush();
    }

    /**
     * @param Loan $loan
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Loan $loan): void
    {
        $em = $this->getEntityManager();
        $em->remove($loan);
        $em->flush();
    }

    /**
     * @param UuidInterface $loanId
     * @throws LoanNotFoundException
     * @return Loan
     */
    public function getLoan(UuidInterface $loanId): Loan
    {
        if ($loan = $this->find($loanId)) {
            return $loan;
        }

        throw new LoanNotFoundException('Loan not found');
    }

    /**
     * @return array
     */
    public function listLoans(): array
    {
        return $this
        ->createQueryBuilder('l')
        ->innerJoin('l.item', 'i')
        ->select('i.id, i.name, l.loaner, l.loanDate, l.returnDate')
        ->orderBy('l.loanDate' ,'ASC')
        ->getQuery()
        ->execute();
    }

    /**
     * @return Query
     */
    public function listAllLoans(?string $searchQuery = ''): Query
    {
        if (empty($searchQuery)) {
            return $this->createQueryBuilder('l')->orderBy('l.loanDate','ASC')->getQuery();
        }

        $qb = $this->createQueryBuilder('l')->innerJoin('l.item', 'i');
        return $qb->where($qb->expr()->like('i.name', ':searchQuery'))
        ->orWhere($qb->expr()->like('l.loaner', ':searchQuery'))
        ->orderBy('l.loanDate' ,'ASC')
        ->setParameters([
            'searchQuery' => "%{$searchQuery}%",
        ])
        ->getQuery();
    }
}
