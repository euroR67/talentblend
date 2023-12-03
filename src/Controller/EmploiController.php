<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Entity\Postule;
use App\Form\EmploiType;
use App\Entity\Categorie;
use App\Entity\Entreprise;
use App\Form\SearchEmploiType;
use App\Repository\EmploiRepository;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/emploi')]
class EmploiController extends AbstractController
{
    // Méthode pour lister les offres d'emplois par catégorie
    #[Route('/categorie/{id}', name: 'app_emplois_par_categorie')]
    public function emploisParCategorie(Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les emplois par catégorie
        $emplois = $entityManager->getRepository(Emploi::class)->findBy(['categories' => $categorie]);

        return $this->render('emploi/emplois_par_categorie.html.twig', [
            'emplois' => $emplois,
            'categorie' => $categorie,
        ]);
    }

    // Méthode pour lister les offres d'emplois par entreprise
    #[Route('/entreprise/{id}', name: 'app_emplois_par_entreprise')]
    public function emploisParEntreprise(Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les emplois par catégorie
        $emplois = $entityManager->getRepository(Emploi::class)->findBy(['entreprise' => $entreprise]);

        return $this->render('emploi/emplois_par_entreprise.html.twig', [
            'emplois' => $emplois,
            'entreprise' => $entreprise,
        ]);
    }

    // Méthode pour afficher le détail d'une offre d'emploi
    #[Route('/detail/{id}', name: 'app_show_emploi')]
    public function showEmploi($id, EntityManagerInterface $entityManager, EmploiRepository $er): Response
    {
        // Récupère l'emploi en question
        $emploi = $er->find($id);

        $user = $this->getUser();

        // Récupérer tout les emplois de l'entreprise de cet emplois
        $entreprise = $emploi->getEntreprise();
        $emploisDeLEntreprise = $entreprise->getEmplois();

        // Vérifiez si l'utilisateur a déjà postulé à cet emploi
        $dejaPostuler = $entityManager->getRepository(Postule::class)->findOneBy(['userPostulant' => $user, 'emploi' => $emploi]);

        // Vérifier si l'utilisateur n'a pas déjà sauvegarder cet emploi
        $alreadySaved = $emploi->getUsers()->contains($user);

        return $this->render('emploi/show.html.twig', [
            'emploi' => $emploi,
            'emploisDeLEntreprise' => $emploisDeLEntreprise,
            'dejaPostuler' => $dejaPostuler,
            'alreadySaved' => $alreadySaved,
        ]);
    }
}
