<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\CandidatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatController extends AbstractController
{
    #[Route('/candidat', name: 'app_candidat_profil')]
    public function profil(): Response
    {
        return $this->render('candidat/profil.html.twig');
    }

    // Méthode pour modifier le profil du candidat
    #[Route('/candidat/{id}/edit', name: 'app_candidat_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidat_edit', ['id' => $user->getId()]);
        }
        
        // Si le formulaire n'est pas soumis, on affiche le formulaire
        return $this->render('candidat/profil.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
