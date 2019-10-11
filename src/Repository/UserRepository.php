<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Repository\Exception\UserNotFoundException;
use Ramsey\Uuid\UuidInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param UuidInterface $userId
     * @throws UserNotFoundException
     * @return User
     */
    public function getUser(UuidInterface $userId): User
    {
        if ($user = $this->find($userId)) {
            return $user;
        }

        throw new UserNotFoundException('User not found');
    }

    /**
     * @param string $email
     * @throws UserNotFoundException
     * @return User
     */
    public function getUserByEmail(string $email): User
    {
        if ($user = $this->findOneBy(['email' => $email])) {
            return $user;
        }

        throw new UserNotFoundException('User not found');
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(User $user): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(User $user): void
    {
        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();
    }
}
