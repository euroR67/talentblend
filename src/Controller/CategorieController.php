<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    // Méthde pour lister toutes les catégories d'emplois
    #[Route('/categories/liste', name: 'app_categories')]
    public function liste(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les catégories
        $categories = $entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Méthode pour lister les catégories d'emplois les plus populaires
    #[Route('/home', name: 'app_categorie_populaire')]
    public function listCategories(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findCategoriesPopulaires();

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
