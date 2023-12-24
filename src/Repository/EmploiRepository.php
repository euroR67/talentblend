<?php

namespace App\Repository;

use App\Entity\Emploi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Emploi>
 *
 * @method Emploi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emploi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emploi[]    findAll()
 * @method Emploi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emploi::class);
    }

    /** Fonction de recherche offre d'emploi par poste et ville
     * @return Emploi[] Returns an array of Emploi objects
     */
    public function searchByPosteAndVille($poste, $ville)
    {
        $qb = $this->createQueryBuilder('e');

        if ($poste) {
            $qb->andWhere('e.poste LIKE :poste')
                ->setParameter('poste', '%'.$poste.'%');
        }

        if ($ville) {
            $qb->join('e.ville', 'v')
                ->andWhere('v.nomVille LIKE :ville')
                ->setParameter('ville', '%'.$ville.'%');
        }

        return $qb->getQuery()->getResult();
    }
    

    // Trouver les emplois non expirés de l'utilisateur en session
    public function findEmploiNonExpirer($emplois)
    {
        // On crée un queryBuilder
        $qb = $this->createQueryBuilder('e');

        // On crée une requête pour trouver les emplois non expirés
        $qb->andWhere('e.dateExpiration > :date')
            // Permet de récupérer la date du jour
            ->setParameter('date', new \DateTime('now'))
            // Permet de trier les emplois par date d'expiration
            ->orderBy('e.dateExpiration', 'DESC');

        // On récupère les emplois de l'utilisateur en session
        $qb->andWhere('e IN (:emplois)')
            ->setParameter('emplois', $emplois)
            // Permet de trier les emplois par date d'expiration
            ->orderBy('e.dateExpiration', 'DESC');

        // On retourne les résultats
        return $qb->getQuery()->getResult();
    }

    // Trouver d'emploi dont la date d'expiration est supérieur à la date du jour et qui appartient a l'utilisateur en session
    public function findEmploiExpirer($emplois)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->andWhere('e.dateExpiration < :date')
            ->setParameter('date', new \DateTime('now'))
            ->orderBy('e.dateExpiration', 'DESC');

        $qb->andWhere('e IN (:emplois)')
            ->setParameter('emplois', $emplois)
            ->orderBy('e.dateExpiration', 'DESC');

        return $qb->getQuery()->getResult();
    }

    // =================== REQUETE SQL de findEmploisByDateExpiration ===================
    // SELECT *
    // FROM `emploi`
    // WHERE date_expiration < NOW() AND id IN 
    // (SELECT id 
    //     FROM `emploi`
    //     WHERE user_id = 14)
    // =================== REQUETE SQL de findEmploisByDateExpiration ===================
    

//    /**
//     * @return Emploi[] Returns an array of Emploi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Emploi
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
