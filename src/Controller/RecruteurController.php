<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emploi;
use App\Entity\Postule;
use App\Form\EmploiType;
use App\Form\SearchCandidatType;
use App\Repository\UserRepository;
use App\Repository\EmploiRepository;
use App\Repository\PostuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/recruteur')]
class RecruteurController extends AbstractController
{
    // Méthode pour afficher la liste des candidatures reçues
    #[Route('/candidatures/liste', name: 'app_candidatures_recu')]
    public function listeCandidatures(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupère les emplois créés par l'utilisateur
        $emplois = $user->getEmplois();

        // Initialise un tableau pour stocker toutes les candidatures
        $candidatures = [];

        // Parcours chaque emploi pour récupérer les postulations
        foreach ($emplois as $emploi) {
            $postulations = $emploi->getPostulations();
            $candidatures = array_merge($candidatures, $postulations->toArray());
        }

        return $this->render('recruteur/liste_candidatures.html.twig', [
            'candidatures' => $candidatures,
        ]);
    }

    // Méthode pour approuvé une candidature
    #[Route('/candidatures/approuver/{id}', name: 'app_approuver')]
    public function approuver($id, Request $request, EntityManagerInterface $entityManager, PostuleRepository $postuleRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupère la postulation à partir de l'ID
        $postulation = $postuleRepository->find($id);

        if (!$postulation) {
            throw $this->createNotFoundException('La postulation demandée n\'existe pas');
        }

        // Approuve la candidature
        $postulation->setStatus(true);

        // Enregistre les modifications
        $entityManager->flush();

        // Redirige vers la liste des candidatures
        return $this->redirectToRoute('app_candidatures_recu');
    }

    // Méthode pour rejeter une candidature
    #[Route('/candidatures/rejeter/{id}', name: 'app_rejeter')]
    public function rejeter($id, Request $request, EntityManagerInterface $entityManager, PostuleRepository $postuleRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupère la postulation à partir de l'ID
        $postulation = $postuleRepository->find($id);

        if (!$postulation) {
            throw $this->createNotFoundException('La postulation demandée n\'existe pas');
        }

        // Approuve la candidature
        $postulation->setStatus(0);

        // Enregistre les modifications
        $entityManager->flush();

        // Redirige vers la liste des candidatures
        return $this->redirectToRoute('app_candidatures_recu');
    }

    // Méthode pour afficher la liste des emplois créers par le recruteur
    #[Route('/emploi/liste', name: 'app_emplois')]
    public function listEmplois(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupère les emplois non expirés créés par l'utilisateur
        $emplois = $entityManager->getRepository(Emploi::class)->findEmploiNonExpirer($user->getEmplois());

        // Récupère les emplois dont dateExpiration est supérieur à la date du jour
        $emploisExpirer = $entityManager->getRepository(Emploi::class)->findEmploiExpirer($user->getEmplois());

        return $this->render('recruteur/index.html.twig', [
            'emplois' => $emplois,
            'emploisExpirer' => $emploisExpirer,
        ]);
    }

    // Méthode pour ajouter / editer une emploi
    #[Route('/emploi/new', name: 'app_new_emploi')]
    #[Route('/emploi/edit/{id}', name: 'app_edit_emploi')]
    public function new_edit_emploi(Emploi $emploi = null, Request $request,EntityManagerInterface $entityManager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Vérifie si l'emploi existe, sinon on en crée un nouveau
        if(!$emploi) {
            $emploi = new Emploi();
            $emploi->setUser($this->getUser());
        }

        // Vérfie si l'emploi appartient à l'utilisateur connecté
        $hasAccess = $emploi->getUser() === $this->getUser();
        
        // Vérifie si l'utilisateur connecté a le droit de modifier l'emploi
        if(!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de modifier cet emploi.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(EmploiType::class, $emploi);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($emploi);

            $entityManager->flush();

            $this->addFlash('success', 'Le nouvelle emploi a bien été ajouter.');

            return $this->redirectToRoute('app_emplois');

        }

        return $this->render('emploi/new.html.twig', [
            'form' => $form,
            'edit' => $emploi->getId()
        ]);
    }

    // Fonction pour supprimer un emploi
    #[Route('/emploi/delete/{id}', name: 'app_delete_emploi')]
    public function delete_emploi(Emploi $emploi = null, Request $request,EntityManagerInterface $entityManager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');
        
        // Vérfie si l'emploi appartient à l'utilisateur connecté
        $hasAccess = $emploi->getUser() === $this->getUser();
        
        if(!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de supprimer cet emploi.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifie si l'emploi existe
        if(!$emploi) {
            throw $this->createNotFoundException('L\'emploi n\'existe pas.');
        }

        // Vérifie si l'utilisateur connecté est différent du créateur de l'emploi
        if($emploi->getUser() !== $user) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de supprimer cet emploi.');
            return $this->redirectToRoute('app_home');
        }

        $entityManager->remove($emploi);
        
        $entityManager->flush();

        // Ajoute un message de succès
        $this->addFlash('success', 'L\'emploi a été supprimé avec succès.');

        return $this->redirectToRoute('app_emplois');
    }

    // Méthode pour afficher le détail d'un candidat
    #[Route('/detail_candidat/{id}', name: 'app_show_candidat')]
    public function showCandidat($id ,Request $request, EntityManagerInterface $entityManager, UserRepository $ur): Response
    {
        // Accès réservé aux recruteurs et aux administrateurs
        if(!$this->isGranted('ROLE_RECRUTEUR') && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('Vous n\'avez pas le droit d\'accéder à cette page.');
        }

        // Récupère le candidat en question
        $candidat = $ur->find($id);

        return $this->render('candidat/show.html.twig', [
            'candidat' => $candidat,
        ]);
    }

    // Méthode pour rechercher un candidat
    #[Route('/search_candidat', name: 'app_search_candidat')]
    public function search(Request $request, PaginatorInterface $paginator, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SearchCandidatType::class);

        // Retrouvez les paramètres GET dans la requête
        $metiers = $request->query->get('metiers');
        $villes = $request->query->get('villes');

        // Retrouvez les paramètres GET directement depuis le Request
        $postData = $request->query->all();

        // Définir des valeurs par défaut si les paramètres ne sont pas présents
        $metiers = $postData['metiers'] ?? '';
        $villes = $postData['villes'] ?? '';

        // Peuplez le formulaire avec les paramètres GET
        $form->get('metiers')->setData($metiers);
        $form->get('villes')->setData($villes);

        $data = []; // initialisez le tableau des données

        $form->handleRequest($request);

        $searchInfo = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Vérifiez si au moins un champ est renseigné
            if (!empty($data['metiers']) || !empty($data['villes'])) {
                $searchInfo = sprintf('%s - %s', $data['metiers'], $data['villes']);

                // Utilisez Doctrine pour effectuer la recherche en fonction du metiers et de la villes
                $results = $paginator->paginate(
                    $entityManager->getRepository(User::class)
                        ->searchByMetiersAndVilles($data['metiers'], $data['villes']),
                    $request->query->getInt('page', 1),
                    1
                );
            } else {
                // Aucun champ renseigné, ne lancez pas la recherche
                $results = [];
            }

            return $this->render('recruteur/candidat_results.html.twig', [
                'results' => $results,
                'searchInfo' => $searchInfo,
                'form' => $form->createView(), // Passez le formulaire à la vue
                'data' => $data, // Passez les données pré-remplies à la vue
            ]);
        }

        return $this->render('recruteur/search_candidat.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour définir le status d'une offre d'emploi
    #[Route('/emploi/status/{id}', name: 'app_status_emploi')]
    public function status($id, Request $request, EntityManagerInterface $entityManager, EmploiRepository $emploiRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupère l'emploi à partir de l'ID
        $emploi = $emploiRepository->find($id);

        if (!$emploi) {
            throw $this->createNotFoundException('L\'emploi demandée n\'existe pas');
        }

        // Vérifie si l'emploi appartient à l'utilisateur connecté
        $hasAccess = $emploi->getUser() === $this->getUser();

        if (!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de modifier cet emploi.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifie si l'emploi est actif ou non
        if ($emploi->isStatus() == true) {
            $emploi->setStatus(false);
        } else {
            $emploi->setStatus(true);
        }

        // Enregistre les modifications
        $entityManager->flush();

        // Redirige vers la liste des emplois
        return $this->redirectToRoute('app_emplois');
    }
}
