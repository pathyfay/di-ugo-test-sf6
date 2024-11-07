<?php

namespace App\Repository;

use App\Entity\RelationChild;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RelationChildRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelationChild::class);
    }

    /**
     * Récupère tous les enfants liés à un utilisateur (comme parent)
     */
    public function findChildrenByParent(int $parentId): array
    {
        return $this->createQueryBuilder('rc')
            ->leftJoin('rc.parents', 'p')
            ->addSelect('p')
            ->andWhere('p.id = :pid')
            ->setParameter('pid', $parentId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les enfants d’une relation spécifique
     */
    public function findByRelation(int $relationId): array
    {
        return $this->createQueryBuilder('rc')
            ->leftJoin('rc.child', 'c')
            ->addSelect('c')
            ->andWhere('rc.relation = :rid')
            ->setParameter('rid', $relationId)
            ->getQuery()
            ->getResult();
    }
}