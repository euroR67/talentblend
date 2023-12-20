<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Entity\Categorie;
use App\Form\SearchEmploiType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    // Renvoyer les informations de la page d'accueil
    #[Route('/', name: 'app_home')]
    public function home(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository)
    {
        $isHomePage = true; // initialisez la variable de page d'accueil

        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        // Récupérez les emplois sauvegardés par l'utilisateur connecté
        if ($user) {
            $savedEmplois = $user->getEmploiSauvegarder();
        } else {
            $savedEmplois = [];
        }

        // Récupérez les catégories populaires
        $categories = $categorieRepository->findCategoriesPopulaires();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'savedEmplois' => $savedEmplois,
            'isHomePage' => $isHomePage,
        ]);
    }
}
