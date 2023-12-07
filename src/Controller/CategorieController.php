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
}
