<?php

namespace App\Repository;

use App\Entity\Postule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Postule>
 *
 * @method Postule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postule[]    findAll()
 * @method Postule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postule::class);
    }

//    /**
//     * @return Postule[] Returns an array of Postule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Postule
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
