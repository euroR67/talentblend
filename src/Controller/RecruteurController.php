<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emploi;
use App\Entity\Postule;
use App\Form\EmploiType;
use App\Repository\UserRepository;
use App\Repository\PostuleRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('emploi/liste', name: 'app_emplois')]
    public function listEmplois(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupère les emplois de l'utilisateur
        $emplois = $user->getEmplois();

        return $this->render('recruteur/index.html.twig', [
            'emplois' => $emplois,
        ]);
    }

    // Méthode pour ajouter / editer une emploi
    #[Route('emploi/new', name: 'app_new_emploi')]
    #[Route('emploi/edit/{id}', name: 'app_edit_emploi')]
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
    #[Route('emploi/delete/{id}', name: 'app_delete_emploi')]
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
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère le candidat en question
        $candidat = $ur->find($id);

        return $this->render('recruteur/show.html.twig', [
            'candidat' => $candidat,
        ]);
    }
}
