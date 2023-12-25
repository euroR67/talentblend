<?php

namespace App\Repository;

use App\Entity\Emploi;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface
    ) {
        parent::__construct($registry, Emploi::class);
    }


    /** Fonction de recherche offre d'emploi par poste et ville
     * @return Emploi[] Returns an array of Emploi objects
     */
    public function searchByPosteAndVille($poste, $ville, $typeEmplois, $contrats): array
    {
        $data = $this->createQueryBuilder('e')
            ->where('e.poste LIKE :poste')
            ->setParameter('poste', "%{$poste}%")
            ->addOrderBy('e.dateOffre', 'DESC');

        if(!empty($poste)) {
            // Recherche par poste
            $data = $data
                ->andWhere('e.poste LIKE :poste')
                ->setParameter('poste', "%{$poste}%");
        }

        if(!empty($ville)) {
            // Recherche par ville
            $data = $data
                ->join('e.ville', 'v')
                ->andWhere('v.nomVille LIKE :ville')
                ->setParameter('ville', "%{$ville}%");
        }

        if(!empty($typeEmplois)) {
            // Recherche par type d'emploi
            $data = $data
                ->join('e.types', 't')
                ->andWhere('t.type IN (:typeEmplois)')
                ->setParameter('typeEmplois', $typeEmplois);
        }
        
        if(!empty($contrats)) {
            // Recherche par contrat
            $data = $data
                ->join('e.contrats', 'c')
                ->andWhere('c.type IN (:contrats)')
                ->setParameter('contrats', $contrats);
        }

        $data = $data
            ->getQuery()
            ->getResult();

        return $data;
    }


    // /** Fonction de recherche offre d'emploi par poste et ville
    //  * @param SearchData $searchData
    //  * @return PaginationInterface
    //  */
    // public function findBySearch(SearchData $searchData): PaginationInterface
    // {
    //     $data = $this->createQueryBuilder('e')
    //         ->where('e.poste LIKE :poste')
    //         ->setParameter('poste', "%{$searchData->poste}%")
    //         ->addOrderBy('e.dateOffre', 'DESC');

    //     if(!empty($searchData->poste)) {
    //         // Recherche par poste
    //         $data = $data
    //             ->andWhere('e.poste LIKE :poste')
    //             ->setParameter('poste', "%{$searchData->poste}%");
    //     }

    //     if(!empty($searchData->ville)) {
    //         // Recherche par ville
    //         $data = $data
    //             ->join('e.ville', 'v')
    //             ->andWhere('v.nomVille LIKE :ville')
    //             ->setParameter('ville', "%{$searchData->ville}%");
    //     }

    //     $data = $data
    //         ->getQuery()
    //         ->getResult();

    //     $emplois = $this->paginatorInterface->paginate(
    //         $data,
    //         $searchData->page,
    //         12
    //     );

    //     return $emplois;
    // }
    

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
