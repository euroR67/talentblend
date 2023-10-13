<?php

namespace App\Repository;

use App\Entity\Represente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Represente>
 *
 * @method Represente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Represente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Represente[]    findAll()
 * @method Represente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepresenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Represente::class);
    }

//    /**
//     * @return Represente[] Returns an array of Represente objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Represente
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
