<?php

namespace App\Repository;

use App\Entity\Emploi;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
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
        private PaginatorInterface $paginatorInterface,
        Security $security
    ) {
        parent::__construct($registry, Emploi::class);
        $this->security = $security;
    }
    


    /** Fonction de recherche offre d'emploi par poste et ville
     * Fonction qui permet également de filtrer les emplois
     * @return Emploi[] Renvoi un tableau d'objet Emploi
     */
    public function findBySearch($poste, $ville, $typeEmplois, $contrats, $dateOffre): array
    {
        // Obtenez la date du jour
        $currentDate = new \DateTime();

        // On crée un queryBuilder et on exclut les offres d'emploi expirées et en pause
        $data = $this->createQueryBuilder('e')
            ->where('e.poste LIKE :poste')
            ->setParameter('poste', "%{$poste}%")
            ->andWhere('e.dateExpiration > :currentDate')
            ->andWhere('e.pause = :isPause')
            ->setParameter('currentDate', $currentDate)
            ->setParameter('isPause', false)
            ->addOrderBy('e.dateOffre', 'DESC');

        // Si le champ ville est renseignée
        if(!empty($ville)) {
            // Recherche par ville
            $data = $data
                ->join('e.ville', 'v')
                ->andWhere('v.nomVille LIKE :ville')
                ->setParameter('ville', "%{$ville}%");
        }

        // Partie filtres
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
            // Récupère la date de début pour la comparaison
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
            // Ces deux lignes permettent d'établir une plage de recherche qui inclus
            // les offres d'emploi publiées jusqu'à la fin de la journée actuelle
            // sans déborder sur les offres d'emploi publiées le lendemain
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
    public function findEmploiNonExpirer()
    {
        // Récupère l'utilisateur en session
        $user = $this->security->getUser();

        // On crée un queryBuilder
        $qb = $this->createQueryBuilder('e');

        // On crée une requête pour trouver les emplois non expirés
        $qb->andWhere('e.dateExpiration > :date')
            // Exclure les emploi qui sont en pause
            ->andWhere('e.pause = :isPause')
            // On récupère les emplois de l'utilisateur en session uniquement
            ->andWhere('e.user = :user')
            // Permet de récupérer la date du jour
            ->setParameter('date', new \DateTime('now'))
            // Permet de trier les emplois par date d'expiration
            ->setParameter('isPause', false)
            ->setParameter('user', $user)
            ->orderBy('e.dateExpiration', 'DESC');

        // On retourne les résultats
        return $qb->getQuery()->getResult();
    }

    public function findEmploiExpirer()
    {
        // Récupère l'utilisateur en session
        $user = $this->security->getUser();

        $qb = $this->createQueryBuilder('e');

        $qb->andWhere('e.dateExpiration < :date')
            ->andWhere('e.user = :user')
            ->setParameter('date', new \DateTime('now'))
            ->setParameter('user', $user)
            ->orderBy('e.dateExpiration', 'DESC');

        return $qb->getQuery()->getResult();
    }

    // Trouver les emploi qui sont mise en pause
    public function findEmploiPaused()
    {
        // Récupère l'utilisateur en session
        $user = $this->security->getUser();

        $qb = $this->createQueryBuilder('e');

        $qb->andWhere('e.pause = :isPause')
            ->andWhere('e.user = :user')
            ->setParameter('isPause', true)
            ->setParameter('user', $user)
            ->orderBy('e.dateExpiration', 'DESC');

        return $qb->getQuery()->getResult();
    }

    // Trouver les emploi par catégorie
    public function findByCategory($categorie)
    {
        return $this->createQueryBuilder('e')
        ->andWhere('e.pause = :isPause')
        ->andWhere('e.categories = :categorie')
        ->setParameter('isPause', false)
        ->setParameter('categorie', $categorie)
        ->orderBy('e.dateOffre', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function findByFilter($categorie, $typeEmplois, $contrats, $dateOffre, $entreprise = null)
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->andWhere('e.categories = :categorie')
            ->andWhere('e.pause = :isPause')
            ->setParameter('categorie', $categorie)
            ->setParameter('isPause', false);

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
