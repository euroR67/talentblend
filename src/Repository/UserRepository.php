<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

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
    public function __construct(ManagerRegistry $registry)
    {
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

    /** Fonction de recherche offre d'emploi par poste et ville
     * @return Emploi[] Returns an array of Emploi objects
     */
    public function searchByMetiersAndVilles($metiers, $villes)
    {
        $qb = $this->createQueryBuilder('e');

        if ($metiers) {
            $qb->join('e.metiers', 'm')
                ->andWhere('m.nomMetier LIKE :metiers')
                ->setParameter('metiers', '%'.$metiers.'%');
        }

        if ($villes) {
            $qb->join('e.villes', 'v')
                ->andWhere('v.nomVille LIKE :villes')
                ->setParameter('villes', '%'.$villes.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
