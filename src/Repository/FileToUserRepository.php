<?php

namespace App\Repository;

use App\Entity\FileToUser;
use App\Enum\FileStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FileToUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileToUser::class);
    }

    public function findCreatedWithFileModelNames(array $names): array
    {
        $qb = $this->createQueryBuilder('f')
            ->innerJoin('f.fileModel', 'fm')
            ->where('f.fileStatus = :status')
            ->andWhere('fm.name IN (:names)')
            ->setParameter('status', FileStatusEnum::CREATED)
            ->setParameter('names', $names)
            ->orderBy('f.dateCreated', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findDistinctTypes(): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('DISTINCT f.fileType');

        $results = $qb->getQuery()->getScalarResult();

        return array_map(fn($row) => $row['fileType'], $results);
    }

    public function findDistinctModel(): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('DISTINCT f.fileModel');

        $results = $qb->getQuery()->getScalarResult();

        return array_map(fn($row) => $row['fileModel'], $results);
    }

}
