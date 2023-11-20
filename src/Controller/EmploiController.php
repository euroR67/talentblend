<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Form\EmploiType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[IsGranted('ROLE_RECRUTEUR')]
#[Route('/recruteur/emploi')]
class EmploiController extends AbstractController
{
    // Méthode pour afficher la liste des emplois créers par le recruteur
    #[Route('/liste', name: 'app_emplois')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Si l'utilisateur n'est pas connecté
        if(!$this->getUser()) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_login');
        }

        // Si l'utilisateur courant est différent de l'utilisateur dont on veut afficher les emplois
        if($this->getUser() !== $user) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        $emplois = $user->getEmplois();

        return $this->render('recruteur/index.html.twig', [
            'emplois' => $emplois,
        ]);
    }

    // Méthode pour ajouter / supprimer un emploi
    #[Route('/new', name: 'app_new_emploi')]
    #[Route('/edit/{id}', name: 'app_edit_emploi')]
    public function new_edit_emploi(Emploi $emploi = null, Request $request,EntityManagerInterface $entityManager) : Response
    {
        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Si l'utilisateur n'est pas connecté
        if(!$this->getUser()) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_login');
        }

        // Si l'utilisateur courant est différent de l'utilisateur
        if($this->getUser() !== $user) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        if(!$emploi) {
            $emploi = new Emploi();
            $emploi->setUser($this->getUser());
        }

        $form = $this->createForm(EmploiType::class, $emploi);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($emploi);

            $entityManager->flush();

            $this->addFlash('success', 'Le nouvelle emploi a bien été ajouter.');

            return $this->redirectToRoute('app_emplois', ['id' => $user->getId()]);

        }

        return $this->render('recruteur/new-emploi.html.twig', [
            'form' => $form,
            'edit' => $emploi->getId()
        ]);
    }

    // Fonction pour supprimer un emploi
    #[Route('/delete/{id}', name: 'app_delete_emploi')]
    public function delete_emploi(Emploi $emploi = null, Request $request,EntityManagerInterface $entityManager) : Response
    {
        // Récupère l'utilisateur en session
        $user = $this->getUser();
        
        // Si l'utilisateur n'est pas connecté
        if(!$this->getUser()) {
            // On renvoi vers la page login
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si l'emploi existe
        if(!$emploi) {
            throw $this->createNotFoundException('L\'emploi n\'existe pas.');
        }

        // Vérifie si l'utilisateur connecté est différent du créateur de l'emploi
        if($emploi->getUser() !== $user) {
            throw new AccessDeniedHttpException('Vous n\'avez pas le droit de supprimer cet emploi.');
        }

        $entityManager->remove($emploi);
        $entityManager->flush();

        // Ajoute un message de succès
        $this->addFlash('success', 'L\'emploi a été supprimé avec succès.');

        return $this->redirectToRoute('app_emplois');
    }
}
