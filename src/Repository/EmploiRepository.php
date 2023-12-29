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
    public function findBySearch($poste, $ville, $typeEmplois, $contrats, $dateOffre): array
    {
        // Obtenez la date du jour
        $currentDate = new \DateTime();

        // On crée un queryBuilder et on exclut les offres d'emploi expirées
        $data = $this->createQueryBuilder('e')
            ->where('e.poste LIKE :poste')
            ->setParameter('poste', "%{$poste}%")
            ->andWhere('e.dateExpiration > :currentDate')
            ->andWhere('e.pause = :isPause')
            ->setParameter('currentDate', $currentDate)
            ->setParameter('isPause', false)
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

        // Recherche par date de publication, Aujourd'hui, 3 derniers jours, La semaine dernière
        if (!empty($dateOffre)) {
            // Obtenez la date de début pour la comparaison
            $startOfRange = new \DateTime();
        
            switch ($dateOffre) {
                case 'Aujourd\'hui':
                    // Recherche pour la journée actuelle
                    $startOfRange->setTime(0, 0, 0);
                    break;
                case '3 derniers jours':
                    // Recherche pour les 3 derniers jours
                    $startOfRange->modify('-2 days')->setTime(0, 0, 0);
                    break;
                case 'La semaine dernière':
                    // Recherche pour les 7 derniers jours (la semaine dernière)
                    $startOfRange->modify('-6 days')->setTime(0, 0, 0);
                    break;
            }
        
            // Obtenez la date de fin pour la comparaison (fin de la journée actuelle)
            $endOfRange = new \DateTime('tomorrow');
            $endOfRange->modify('-1 second');
        
            // Recherche par date de publication
            $data = $data->andWhere('e.dateOffre BETWEEN :startOfRange AND :endOfRange')
                ->setParameter('startOfRange', $startOfRange)
                ->setParameter('endOfRange', $endOfRange);
        }
        
        // Récupérez les résultats
        $data = $data
            ->getQuery()
            ->getResult();

        // Retournez les résultats
        return $data;
    }

    // Trouver les emplois non expirés de l'utilisateur en session
    public function findEmploiNonExpirer($emplois)
    {
        // On crée un queryBuilder
        $qb = $this->createQueryBuilder('e');

        // On crée une requête pour trouver les emplois non expirés
        $qb->andWhere('e.dateExpiration > :date')
            // Exclure les emploi qui sont en pause
            ->andWhere('e.pause = :isPause')
            // Permet de récupérer la date du jour
            ->setParameter('date', new \DateTime('now'))
            // Permet de trier les emplois par date d'expiration
            ->setParameter('isPause', false)
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

    // Trouver les emploi qui sont mise en pause
    public function findEmploiPaused($emplois)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->andWhere('e.pause = :isPause')
            ->setParameter('isPause', true)
            ->orderBy('e.dateExpiration', 'DESC');

        $qb->andWhere('e IN (:emplois)')
            ->setParameter('emplois', $emplois)
            ->orderBy('e.dateExpiration', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findByFilter($categorie, $typeEmplois, $contrats, $dateOffre, $entreprise = null)
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->andWhere('e.categories = :categorie')
            ->setParameter('categorie', $categorie);

        if (!empty($entreprise)) {
            $queryBuilder = $this->createQueryBuilder('e')
                ->andWhere('e.entreprise = :entreprise')
                ->setParameter('entreprise', $entreprise);
        }

        if (!empty($typeEmplois)) {
            $queryBuilder
                ->join('e.types', 't')
                ->andWhere('t.type IN (:typeEmplois)')
                ->setParameter('typeEmplois', $typeEmplois);
        }

        if (!empty($contrats)) {
            $queryBuilder
                ->join('e.contrats', 'c')
                ->andWhere('c.type IN (:contrats)')
                ->setParameter('contrats', $contrats);
        }

        // Recherche par date de publication, Aujourd'hui, 3 derniers jours, La semaine dernière
        if (!empty($dateOffre)) {
            // Obtenez la date de début pour la comparaison
            $startOfRange = new \DateTime();
        
            switch ($dateOffre) {
                case 'Aujourd\'hui':
                    // Recherche pour la journée actuelle
                    $startOfRange->setTime(0, 0, 0);
                    break;
                case '3 derniers jours':
                    // Recherche pour les 3 derniers jours
                    $startOfRange->modify('-2 days')->setTime(0, 0, 0);
                    break;
                case 'La semaine dernière':
                    // Recherche pour les 7 derniers jours (la semaine dernière)
                    $startOfRange->modify('-6 days')->setTime(0, 0, 0);
                    break;
            }
        
            // Obtenez la date de fin pour la comparaison (fin de la journée actuelle)
            $endOfRange = new \DateTime('tomorrow');
            $endOfRange->modify('-1 second');
        
            // Recherche par date de publication
            $queryBuilder = $queryBuilder->andWhere('e.dateOffre BETWEEN :startOfRange AND :endOfRange')
                ->setParameter('startOfRange', $startOfRange)
                ->setParameter('endOfRange', $endOfRange);
        }
        
        $queryBuilder->orderBy('e.dateOffre', 'DESC');
        // Exécutez la requête et retournez les résultats
        return $queryBuilder->getQuery()->getResult();
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
