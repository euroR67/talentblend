<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Entity\Postule;
use App\Form\EmploiType;
use App\Form\FiltreType;
use App\Entity\Categorie;
use App\Entity\Entreprise;
use App\Form\SearchEmploiType;
use App\Repository\EmploiRepository;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/emploi')]
class EmploiController extends AbstractController
{
    // Méthode pour lister les offres d'emplois par catégorie
    #[Route('/categorie/{id}', name: 'app_emplois_par_categorie')]
    public function emploisParCategorie(Categorie $categorie = null, EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator, FormFactoryInterface $formFactory): Response
    {

        // Si la catégorie n'existe pas, renvoyez une erreur 404
        if (!$categorie) {
            return $this->redirectToRoute('app_error404');
        }

        // Créez le formulaire de filtre
        $form = $formFactory->create(FiltreType::class);
        $form->handleRequest($request);

        // Récupérez les valeurs du formulaire de filtre
        $typeEmplois = $form->get('typeEmplois')->getData();
        $contrats = $form->get('contrats')->getData();
        $dateOffre = $form->get('dateOffre')->getData();

        // Si le formulaire n'est pas soumis, affichez tous les emplois de la catégorie
        $emplois = $paginator->paginate(
            $entityManager->getRepository(Emploi::class)->findByCategory($categorie),
            $request->query->getInt('page', 1),
            12
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $emplois = $paginator->paginate(
                $entityManager->getRepository(Emploi::class)->findByFilter($categorie, $typeEmplois, $contrats, $dateOffre),
                $request->query->getInt('page', 1),
                12
            );
        }

        // Récupérez l'utilisateur connecté et les emplois sauvegardés
        $user = $this->getUser();
        $savedEmplois = $user ? $user->getEmploiSauvegarder() : [];

        return $this->render('emploi/emplois_par_categorie.html.twig', [
            'emplois' => $emplois,
            'categorie' => $categorie,
            'savedEmplois' => $savedEmplois,
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour lister les offres d'emplois par entreprise
    #[Route('/entreprise/{id}', name: 'app_emplois_par_entreprise')]
    public function emploisParEntreprise(Entreprise $entreprise = null, EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator, FormFactoryInterface $formFactory): Response
    {

        // Si l'entreprise n'existe pas, renvoyez une erreur 404
        if (!$entreprise) {
            return $this->redirectToRoute('app_error404');
        }

        // Créez le formulaire de filtre
        $form = $formFactory->create(FiltreType::class);
        $form->handleRequest($request);

        // Récupérez les valeurs du formulaire de filtre
        $typeEmplois = $form->get('typeEmplois')->getData();
        $contrats = $form->get('contrats')->getData();
        $dateOffre = $form->get('dateOffre')->getData();

        // Si le formulaire n'est pas soumis, affichez tous les emplois de l'entreprise
        $emplois = $paginator->paginate(
            $entityManager->getRepository(Emploi::class)->findBy(['entreprise' => $entreprise], ['dateOffre' => 'DESC']),
            $request->query->getInt('page', 1),
            12
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $emplois = $paginator->paginate(
                $entityManager->getRepository(Emploi::class)->findByFilter(null, $typeEmplois, $contrats, $dateOffre, $entreprise),
                $request->query->getInt('page', 1),
                12
            );
        }

        // Récupérez l'utilisateur connecté et les emplois sauvegardés
        $user = $this->getUser();
        $savedEmplois = $user ? $user->getEmploiSauvegarder() : [];

        return $this->render('emploi/emplois_par_entreprise.html.twig', [
            'emplois' => $emplois,
            'entreprise' => $entreprise,
            'savedEmplois' => $savedEmplois,
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour afficher le détail d'une offre d'emploi
    #[Route('/detail/{id}', name: 'app_show_emploi')]
    public function showEmploi($id, EntityManagerInterface $entityManager, EmploiRepository $er): Response
    {
        // Si l'emploi n'existe pas, renvoyez une erreur 404
        if (!$er->find($id)) {
            return $this->redirectToRoute('app_error404');
        }

        // Récupère l'emploi en question
        $emploi = $er->find($id);

        // Récupérer l'utilisateur connecté
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