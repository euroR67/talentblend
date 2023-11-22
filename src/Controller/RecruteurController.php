<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emploi;
use App\Entity\Postule;
use App\Form\EmploiType;
use App\Repository\PostuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/recruteur/candidatures')]
class RecruteurController extends AbstractController
{
    // Méthode pour afficher la liste des candidatures reçues
    #[Route('/liste', name: 'app_candidatures_recu')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
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
    #[Route('/approuver/{id}', name: 'app_approuver')]
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
    #[Route('/rejeter/{id}', name: 'app_rejeter')]
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
}
