<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\UsersSearch;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

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
     * @return array
     */
    public function findAllUsers($recherche): array
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.age <= :age')
            ->setParameter('age', $recherche->getAge())
            ->andWhere('u.sexe = :sexe')
            ->setParameter('sexe', $recherche->getSexe())
            ->orderBy('u.age', 'DESC')
            ->getQuery();
        return $qb->execute();
    }
}
