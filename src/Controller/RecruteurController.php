<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emploi;
use App\Entity\Postule;
use App\Form\EmploiType;
use App\Model\SearchData;
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
    // Méthode pour rechercher un candidat
    #[Route('/search_candidat', name: 'app_search_candidat', methods: ['GET'])]
    public function searchCandidat(Request $request, PaginatorInterface $paginator,UserRepository $userRepository ,EntityManagerInterface $entityManager)
    {
        $data = new SearchData();

        $form = $this->createForm(SearchCandidatType::class, $data);

        $form->handleRequest($request);

        $searchInfo = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $data->page = $request->query->getInt('page', 1);

            // Stocker en string les informations de recherche
            $searchInfo = $data->metier . ' ' . $data->ville;
            
            // Si les champs "metier" et "ville" sont tous les deux vides, ne pas effectuer la recherche
            if (empty($data->metier) && empty($data->ville)) {
                $results = [];
            } else {
                $results = $userRepository->findBySearch($data);
            }

            return $this->render('recruteur/candidat_results.html.twig', [
                'results' => $results,
                'searchInfo' => $searchInfo,
                'form' => $form, // Passez le formulaire à la vue
                'data' => $data, // Passez les données pré-remplies à la vue
            ]);
        }

        return $this->render('recruteur/search_candidat.html.twig', [
            'form' => $form,
        ]);
    }
    
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

        // Récupère les emplois qui sont mis en pause
        $emploiPaused = $entityManager->getRepository(Emploi::class)->findEmploiPaused($user->getEmplois());

        return $this->render('recruteur/index.html.twig', [
            'emplois' => $emplois,
            'emploisExpirer' => $emploisExpirer,
            'emploisPaused' => $emploiPaused,
        ]);
    }

    // Méthode pour ajouter / editer une emploi
    #[Route('/emploi/new', name: 'app_new_emploi')]
    #[Route('/emploi/edit/{id}', name: 'app_edit_emploi')]
    public function new_edit_emploi(Emploi $emploi = null, Request $request,EntityManagerInterface $entityManager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Obtenez l'utilisateur actuel
        $user = $this->getUser();

        // Vérifiez si l'utilisateur a une entreprise ou représente une entreprise
        if ($user->getEntrepriseRepresenter()->isEmpty() && $user->getEntrepriseCreator()->isEmpty()) {
            $this->addFlash('warning', 'Vous devez d\'abord créer une entreprise ou une représentation.');
            return $this->redirectToRoute('app_new_represente');
        }
        
        // Vérifie si l'emploi existe, sinon on en crée un nouveau
        if(!$emploi) {
            $emploi = new Emploi();
            $emploi->setDateOffre(new \DateTime());
            $emploi->setDateExpiration(new \DateTime('+1 month'));
            $emploi->setPause(false);
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

        // Vérifie si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()) {
            // Gérer les données concernant le salaire lors de la modification
            if($emploi->getShowBy() == 'negociable') {
                $emploi->setSalaireMinimum(null);
                $emploi->setSalaire(null);
                $emploi->setShowBy('negociable');
            } elseif($emploi->getShowBy() == 'montantMinimum') {
                $emploi->setSalaire(null);
                $emploi->setShowBy('montantMinimum');
            } elseif($emploi->getShowBy() == 'montantMaximum') {
                $emploi->setSalaireMinimum(null);
                $emploi->setShowBy('montantMaximum');
            }

            $entityManager->persist($emploi);

            $entityManager->flush();

            // Vérifie si l'emploi a déjà un ID
            if($emploi->getId()) {
                $this->addFlash('success', 'L\'emploi a bien été modifié.');
            } else {
                $this->addFlash('success', 'L\'offre d\'emploi a bien été ajouté.');
            }

            return $this->redirectToRoute('app_emplois');

        }

        return $this->render('emploi/new.html.twig', [
            'form' => $form,
            'edit' => $emploi->getId()
        ]);
    }

    // Méthode pour prolonger de 1 mois la durée d'une offre emploi
    #[Route('/emploi/prolonger/{id}', name: 'app_extend_emploi')]
    public function extendEmploi(Emploi $emploi, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Accès réservé au recruteur
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Vérifie si l'emploi appartient à l'utilisateur connecté
        $hasAccess = $emploi->getUser() === $this->getUser();

        // Vérifie si l'emploi actuel est expiré ou non
        $isExpired = $emploi->getDateExpiration() < new \DateTime();

        // Si l'emploi est expiré et que l'utilisateur est le créateur de l'emploi
        if($isExpired && $hasAccess) {
            // On ajoute prolonge d'un mois l'offre d'emploi
            $emploi->setDateOffre(new \DateTime());
            $emploi->setDateExpiration(new \DateTime('+1 month'));
        } else {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de modifier cet emploi.');
            return $this->redirectToRoute('app_home');
        }

        // Enregistre les modifications
        $entityManager->flush();

        // Message flash de succès
        $this->addFlash('success', "L'offre d'emploi a été prolongée avec succès pour une période de 30 jours.");

        // Redirige vers la liste des emplois
        return $this->redirectToRoute('app_emplois');
    }
    
    // Fonction pour supprimer un emploi
    #[Route('/emploi/delete/{id}', name: 'app_delete_emploi')]
    public function delete_emploi(Emploi $emploi = null, Request $request,EntityManagerInterface $entityManager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');
        
        // Récupère l'utilisateur en session
        $user = $this->getUser();

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

    // Méthode pour mettre en pause/resume une offre d'emploi
    #[Route('/emploi/pauseResume/{id}', name: 'app_pause_emploi')]
    public function pauseResume(Emploi $emploi, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Accès réservé au recruteur
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Vérifie si l'emploi appartient à l'utilisateur connecté
        $hasAccess = $emploi->getUser() === $this->getUser();

        if(!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de modifier cet emploi.');
            return $this->redirectToRoute('app_home');
        }

        if($emploi->isPause() == true) {
            $emploi->setPause(false);
        } else {
            $emploi->setPause(true);   
        }

        // Enregistre les modifications
        $entityManager->flush();

        // Redirige vers la liste des emplois
        return $this->redirectToRoute('app_emplois');
    }
}
