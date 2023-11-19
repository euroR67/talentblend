<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emploi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecruteurController extends AbstractController
{
    // Méthode pour afficher la liste des emplois créers par le recruteur
    #[Route('/recruteur/{id}', name: 'app_emplois')]
    public function index(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
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
}
