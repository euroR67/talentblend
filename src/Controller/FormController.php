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
        // Récupérez les données de la session
        $data = $request->getSession()->get('search_data', []);

        $isHomePage = $request->query->get('isHomePage', false);

        // Créez le formulaire avec les données de la session
        $formModal = $this->createForm(SearchEmploiType::class, $data);

        // Retrieve GET parameters
        $poste = $request->query->get('poste');
        $ville = $request->query->get('ville');

        // Retrieve GET parameters directly from the request
        $postData = $request->query->all();

        // Set default values if parameters are not present
        $poste = $postData['poste'] ?? '';
        $ville = $postData['ville'] ?? '';

        // Populate form with GET parameters
        $formModal->get('poste')->setData($poste);
        $formModal->get('ville')->setData($ville);

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

            // Stockez les données dans la session pour les réutiliser dans le formulaire
            $request->getSession()->set('search_data', $data);

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

        // Récupérez les données de la session
        $data = $request->getSession()->get('search_data', []);

        // Initialisez le formulaire avec les données de la session
        $formModal = $this->createForm(SearchEmploiType::class, $data);
        

        return $this->render('form/search.html.twig', [
            'formModal' => $formModal->createView(),
            'data' => $data,
            'isHomePage' => $isHomePage,
        ]);
    }
}
?>