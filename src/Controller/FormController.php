<?php
namespace App\Controller;

use App\Entity\Emploi;
use App\Form\SearchEmploiType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormController extends AbstractController
{
    public function searchForm(Request $request, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository)
    {
        $formModal = $this->createForm(SearchEmploiType::class);

        $data = []; // initialisez le tableau des données

        $formModal->handleRequest($request);

        $searchInfo = '';

        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        // Récupérez les emplois sauvegardés par l'utilisateur connecté
        if ($user) {
            $savedEmplois = $user->getEmploiSauvegarder();
        } else {
            $savedEmplois = [];
        }

        if ($formModal->isSubmitted() && $formModal->isValid()) {
            $data = $formModal->getData();

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
                'formModal' => $formModal->createView(), // Passez le formulaire à la vue
                'data' => $data, // Passez les données pré-remplies à la vue
                'savedEmplois' => $savedEmplois, // Passez la variable alreadySaved à la vue
            ]);
        }

        return $this->render('form/search.html.twig', [
            'formModal' => $formModal->createView(),
            'data' => $data,
            'searchInfo' => $searchInfo,
        ]);
    }
}
?>