<?php
namespace App\Controller;

use App\Entity\Emploi;
use App\Form\SearchEmploiType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormController extends AbstractController
{
    public function searchForm(Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager, CategorieRepository $categorieRepository)
    {
        // Récupérez les données de la session
        $data = $request->getSession()->get('search_data', []);

        // S'assurer que typeEmplois est un tableau
        if (isset($data['typeEmplois']) && !is_array($data['typeEmplois'])) {
            $data['typeEmplois'] = [$data['typeEmplois']];
        }

        // S'assurer que contrats est un tableau
        if (isset($data['contrats']) && !is_array($data['contrats'])) {
            $data['contrats'] = [$data['contrats']];
        }

        // S'assurer que dateOffre est un tableau
        if (isset($data['dateOffre']) && !is_array($data['dateOffre'])) {
            $data['dateOffre'] = [$data['dateOffre']];
        }

        $isHomePage = $request->query->get('isHomePage', false);

        // Créez le formulaire avec les données de la session
        $form = $this->createForm(SearchEmploiType::class, $data);

        // Retrouvez les paramètres GET
        $poste = $request->query->get('poste');
        $ville = $request->query->get('ville');

        // Retrouvez les paramètres GET directement depuis la requête
        $postData = $request->query->all();

        // Définissez des valeurs par défaut si les paramètres ne sont pas présents
        $poste = $postData['poste'] ?? '';
        $ville = $postData['ville'] ?? '';

        // Pré-remplissez le formulaire avec les paramètres GET
        $form->get('poste')->setData($poste);
        $form->get('ville')->setData($ville);

        $data = []; // initialisez le tableau des données

        $form->handleRequest($request);

        $searchInfo = '';

        // Récupérez l'utilisateur connecté
        $user = $this->getUser();

        // Récupérez les emplois sauvegardés par l'utilisateur connecté
        if ($user) {
            $savedEmplois = $user->getEmploiSauvegarder();
        } else {
            $savedEmplois = [];
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Filtrez les données avant de les stocker en session
            $searchData = [
                'poste' => $data['poste'],
                'ville' => $data['ville'],
            ];

            // Stockez les données filtrées dans la session pour les réutiliser dans le formulaire
            $request->getSession()->set('search_data', $searchData);

            // Vérifiez si au moins un champ est renseigné
            if (!empty($data['poste']) || !empty($data['ville'])) {
                $searchInfo = sprintf('%s - %s', $data['poste'], $data['ville']);
                
                // Utilisez Doctrine pour effectuer la recherche en fonction du poste et de la ville
                $results = $paginator->paginate(
                    $entityManager->getRepository(Emploi::class)
                        ->findBySearch($data['poste'], $data['ville'], $data['typeEmplois'], $data['contrats'], $data['dateOffre']),
                    $request->query->getInt('page', 1),
                    12
                ); 
            } else {
                // Aucun champ renseigné, ne pas effectuer de recherche
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

        // Récupérez les données de la session
        $data = $request->getSession()->get('search_data', []);

        // Initialisez le formulaire avec les données de la session
        $form = $this->createForm(SearchEmploiType::class, $data);
        

        return $this->render('components/search.html.twig', [
            'form' => $form->createView(),
            'data' => $data,
            'isHomePage' => $isHomePage,
        ]);
    }
}
?>