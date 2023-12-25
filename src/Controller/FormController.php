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

        // Assurez-vous que 'typeEmplois' est un tableau
        if (isset($data['typeEmplois']) && !is_array($data['typeEmplois'])) {
            $data['typeEmplois'] = [$data['typeEmplois']];
        }

        // Assurez-vous que 'contrats' est un tableau
        if (isset($data['contrats']) && !is_array($data['contrats'])) {
            $data['contrats'] = [$data['contrats']];
        }

        $isHomePage = $request->query->get('isHomePage', false);

        // Créez le formulaire avec les données de la session
        $form = $this->createForm(SearchEmploiType::class, $data);

        // Retrieve GET parameters
        $poste = $request->query->get('poste');
        $ville = $request->query->get('ville');

        // Retrieve GET parameters directly from the request
        $postData = $request->query->all();

        // Set default values if parameters are not present
        $poste = $postData['poste'] ?? '';
        $ville = $postData['ville'] ?? '';

        // Populate form with GET parameters
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
                        ->searchByPosteAndVille($data['poste'], $data['ville'], $data['typeEmplois'], $data['contrats']),
                    $request->query->getInt('page', 1),
                    12
                ); 
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