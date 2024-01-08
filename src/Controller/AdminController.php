<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Entity\Represente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    // Fonction pour accéder à l'espace admin et afficher le tableau de bord
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    // Fonction pour lister les entreprise en attente de validation
    #[Route('/admin/entreprise', name: 'app_admin_entreprise')]
    public function listeEntreprise(EntityManagerInterface $entityManager): Response
    {
        // On récupère les entreprises en attente de validation isVerified = 0 ou null
        $entreprises = $entityManager->getRepository(Entreprise::class)->findBy(['isVerified' => null]);

        // On récupère les entreprises refusées
        $entreprisesRefusees = $entityManager->getRepository(Entreprise::class)->findBy(['isVerified' => false]);

        return $this->render('admin/liste_entreprise.html.twig', [
            'entreprises' => $entreprises,
            'entreprisesRefusees' => $entreprisesRefusees,
        ]);
    }

    // Fonction pour voir le détail de l'entreprise en attente de validation
    #[Route('/admin/entreprise/{id}', name: 'app_validation_entreprise')]
    public function detailEntreprise(Entreprise $entreprise): Response
    {
        return $this->render('admin/detail_entreprise.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }

    // Fonction pour valide une entreprise
    #[Route('/admin/entreprise/{id}/valider', name: 'app_valider_entreprise')]
    public function valideEntreprise(Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        // On valide l'entreprise
        $entreprise->setIsVerified(true);
        $entityManager->flush();

        // Message flash
        $this->addFlash('success', 'L\'entreprise a bien été validée');

        // On redirige vers la liste des entreprises en attente de validation
        return $this->redirectToRoute('app_admin_entreprise');
    }

    // Fonction pour refuser une entreprise
    #[Route('/admin/entreprise/{id}/refuser', name: 'app_refuser_entreprise', methods: ['POST'])]
    public function refuseEntreprise(Request $request, Entreprise $entreprise, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le motif de refus du formulaire
        $motifRefus = $request->request->get('motifRefus');

        // Mettre à jour l'entreprise
        $entreprise->setIsVerified(false);
        $entreprise->setMotifRefus($motifRefus);

        // Sauvegarder les modifications
        $entityManager->persist($entreprise);
        $entityManager->flush();

        // Message flash
        $this->addFlash('success', 'L\'entreprise a bien été refusée');

        // On redirige vers la liste des entreprises en attente de validation
        return $this->redirectToRoute('app_admin_entreprise');
    }

    // Fonction pour lister les représentants en attente de validation
    #[Route('/admin/representant', name: 'app_admin_representant')]
    public function listeRepresentant(EntityManagerInterface $entityManager): Response
    {
        // Récupère les objets Represente en attente de validation isStatus = null
        $representations = $entityManager->getRepository(Represente::class)->findBy(['status' => null]);

        // Récupère les objets Represente refusés
        $representationsRefusees = $entityManager->getRepository(Represente::class)->findBy(['status' => false]);

        return $this->render('admin/liste_representation.html.twig', [
            'representations' => $representations,
            'representationsRefusees' => $representationsRefusees,
        ]);
    }

    // Fonction pour voir le détail du représentant en attente de validation
    #[Route('/admin/representant/{id}', name: 'app_validation_representant')]
    public function detailRepresentant(Represente $representation): Response
    {
        return $this->render('admin/detail_representation.html.twig', [
            'representation' => $representation,
        ]);
    }

    // Fonction pour valide un représentant
    #[Route('/admin/representation/{id}/valider', name: 'app_valider_representation')]
    public function valideRepresentant(Represente $representation, EntityManagerInterface $entityManager): Response
    {
        // On valide le représentant
        $representation->setStatus(true);
        $entityManager->flush();

        // Message flash
        $this->addFlash('success', 'La demande de représentation a bien été validé');

        // On redirige vers la liste des représentants en attente de validation
        return $this->redirectToRoute('app_admin_representant');
    }

    // Fonction pour refuser un représentant
    #[Route('/admin/representation/{id}/refuser', name: 'app_refuser_representation', methods: ['POST'])]
    public function refuseRepresentant(Request $request, Represente $representation, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le motif de refus du formulaire
        $motifRefus = $request->request->get('motifRefus');

        // Mettre à jour le représentant
        $representation->setStatus(false);
        $representation->setMotifRefus($motifRefus);

        // Sauvegarder les modifications
        $entityManager->persist($representation);
        $entityManager->flush();

        // Message flash
        $this->addFlash('success', 'La demande de représentation a bien été refusée');

        // On redirige vers la liste des représentants en attente de validation
        return $this->redirectToRoute('app_admin_representant');
    }

}
