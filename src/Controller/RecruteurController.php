<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emploi;
use App\Form\EmploiType;
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
}
