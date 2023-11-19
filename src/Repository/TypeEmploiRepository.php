<?php

namespace App\Repository;

use App\Entity\TypeEmploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeEmploi>
 *
 * @method TypeEmploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeEmploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeEmploi[]    findAll()
 * @method TypeEmploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeEmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEmploi::class);
    }

//    /**
//     * @return TypeEmploi[] Returns an array of TypeEmploi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeEmploi
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
