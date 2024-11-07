<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUsersHavingBirthdayOn(\DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('MONTH(u.dateOfBirth) = :m')
            ->andWhere('DAY(u.dateOfBirth) = :d')
            ->setParameter('m', (int)$date->format('m'))
            ->setParameter('d', (int)$date->format('d'));

        // Option : traiter 29/02 les années non bissextiles (décommente si souhaité)
        // if ($date->format('m-d') === '02-28' && (int)$date->format('L') === 0) {
        //     $qb->orWhere('(MONTH(u.dateOfBirth) = 2 AND DAY(u.dateOfBirth) = 29)');
        // }

        return $qb->getQuery()->getResult();
    }
}
