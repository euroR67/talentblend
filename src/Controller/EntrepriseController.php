<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/recruteur/entreprise')]
class EntrepriseController extends AbstractController
{
    #[Route('/liste', name: 'app_entreprises')]
    public function liste(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupérer les entreprise que représente l'utilisateur / recruteur
        $representes = $user->getRepresentants();

        // Pour chaque entreprise, compter le nombre d'emplois créés par l'utilisateur
        $emploisParEntreprise = [];
        foreach ($representes as $represente) {
            $emplois = $represente->getEntreprise()->getEmplois()->toArray();
            $emploisParEntreprise[$represente->getId()] = count(array_filter($emplois, function($emploi) use ($user) {
                return $emploi->getUser() === $user;
            }));
        }

        return $this->render('entreprise/index.html.twig', [
            'representes' => $representes,
            'emploisParEntreprise' => $emploisParEntreprise,
        ]);
    }

    // Méthode pour ajouter / supprimer un emploi
    #[Route('/new', name: 'app_new_entreprise')]
    #[Route('/edit/{id}', name: 'app_edit_entreprise')]
    public function new_edit_emploi(Emploi $emploi = null, Request $request,EntityManagerInterface $entityManager) : Response
    {
        
    }
}
