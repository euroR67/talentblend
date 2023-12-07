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
    #[Route('/', name: 'app_search')]
    public function search(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository)
    {
        $form = $this->createForm(SearchEmploiType::class);

        $data = []; // initialisez le tableau des données

        $form->handleRequest($request);

        $searchInfo = '';

        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        // Récupérez les emplois sauvegardés par l'utilisateur connecté
        $savedEmplois = $user->getEmploiSauvegarder();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Vérifiez si au moins un champ est renseigné
            if (!empty($data['poste']) || !empty($data['ville'])) {
                $searchInfo = sprintf('%s - %s', $data['poste'], $data['ville']);

                // Utilisez Doctrine pour effectuer la recherche en fonction du poste et de la ville
                $results = $entityManager->getRepository(Emploi::class)
                    ->searchByPosteAndVille($data['poste'], $data['ville']);
            } else {
                // Aucun champ renseigné, ne lancez pas la recherche
                $results = [];
            }

            return $this->render('emploi/emploi_results.html.twig', [
                'results' => $results,
                'searchInfo' => $searchInfo,
                'form' => $form->createView(), // Passez le formulaire à la vue
                'data' => $data, // Passez les données pré-remplies à la vue
                'savedEmplois' => $savedEmplois, // Passez la variable alreadySaved à la vue
            ]);
        }

        $categories = $categorieRepository->findCategoriesPopulaires();

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'data' => $data,
            'searchInfo' => $searchInfo,
            'categories' => $categories,
        ]);
    }
}
