<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string role
     * @return int|mixed|string
     */
    public function findUsersByForbiddenRole($role)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u')
            ->where('u.roles NOT LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string role
     * @return int|mixed|string
     */
    public function findUsersByRole($role)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }
}
