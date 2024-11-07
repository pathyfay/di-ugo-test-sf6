<?php

namespace App\Repository;

use App\Entity\UserRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRelation::class);
    }

    /**
     * Trouver toutes les relations actives d’un utilisateur
     */
    public function findActiveRelationsForUser(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.user1', 'u1')
            ->leftJoin('r.user2', 'u2')
            ->leftJoin('r.user3', 'u3')
            ->leftJoin('r.user4', 'u4')
            ->andWhere('u1.id = :id OR u2.id = :id OR u3.id = :id OR u4.id = :id')
            ->setParameter('id', $userId)
            ->orderBy('r.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
