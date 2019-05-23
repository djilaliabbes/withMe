<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraints\DateTime;




/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }
    //---------------selectionner les messages entre les deux users-----------------------------------
    /**
     * selectionner les messages entre les deux users
     * @return array
     */
    public function findMessages($user, $userTo): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Message m
            WHERE (m.userFrom = :user AND m.userTo = :userTo) OR (m.userFrom = :userTo AND m.userTo = :user)
            ORDER BY m.createdAt ASC
            '
        )->setParameters(array('user' => $user->getId(), 'userTo' => $userTo->getId()));
        return $query->execute();
    }
    //---------------------------obtenir le dernier message envoyé
    /**
     * obtenir le dernier message envoyé
     * @return array
     */
    public function findDernierMsg($user): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.userFrom = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults('1')
            ->getQuery();
        return $qb->execute();
    }
    //-----------------------------obtenir le nombre de message non lu --------------
    /**
     * obtenir le nombre de message non lu
     */
    public function countMsgNonLu($user)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT (m.userFrom ) as userFrom  ,COUNT(m.id) as count
            FROM App\Entity\Message m
            WHERE m.userTo = :user AND m.readAt is null
            GROUP BY m.userFrom
            '
        )
            ->setParameter('user', $user);
        return $query->execute();
    }
    /**
     * marquer les message non Lu comme message lu
     */
    public function updateReadAt($from, $to)
    {
        $date = new \DateTime();
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'UPDATE App\Entity\Message m
            set m.readAt = :date
            WHERE  m.userFrom = :from and  m.userTo = :to
            '
        )
            ->setParameters([
                'date' => $date,
                'from' => $from,
                'to'  => $to
            ]);
        return $query->execute();
    }
}
