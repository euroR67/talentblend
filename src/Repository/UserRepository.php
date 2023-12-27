<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
* @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private PaginatorInterface $paginatorInterface
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    // Requête DQL pour afficher les entreprise que représente un recruteur (utilisateur)
    public function findRecruteurEntreprises(int $id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Entreprise e
            WHERE e.recruteur = :id'
        )->setParameter('id', $id);

        // retourner les résultats sous forme d'un tableau d'objets Entreprise
        return $query->getResult();
    }

    /** Fonction de recherche de candidat par poste et ville et filtres
     * @param SearchData $searchData
     * @return PaginationInterface
     */
    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.metier', 'metier')
            ->leftJoin('u.niveau', 'niveau')
            ->leftJoin('u.ville', 'ville')
            ->leftJoin('u.typesEmploi', 'typesEmploi')
            ->leftJoin('u.contrats', 'contrats');

        // Ajout des conditions basées sur les filtres
        if (!empty($searchData->metier)) {
            $qb->andWhere('metier.nomMetier LIKE :metier')
                ->setParameter('metier', '%' . $searchData->metier . '%');
        }

        if (!empty($searchData->ville)) {
            $qb->andWhere('ville.nomVille LIKE :ville')
                ->setParameter('ville', '%' . $searchData->ville . '%');
        }

        if (!empty($searchData->typesEmploi)) {
            $qb->andWhere('typesEmploi.id IN (:typesEmploi)')
                ->setParameter('typesEmploi', $searchData->typesEmploi);
        }

        if (!empty($searchData->contrats)) {
            $qb->andWhere('contrats.id IN (:contrats)')
                ->setParameter('contrats', $searchData->contrats);
        }

        if (!empty($searchData->niveau)) {
            $qb->andWhere('niveau.id IN (:niveau)')
                ->setParameter('niveau', $searchData->niveau);
        }

        $query = $qb->getQuery();

        $results = $this->paginatorInterface->paginate(
            $query,
            $searchData->page,
            12 // 12 résultats par page
        );

        return $results;
    }
}
