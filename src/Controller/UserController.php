<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserController extends AbstractController
{
    // Méthode pour permettre la modification du mot de passe de l'utilisateur
    #[Route('/user/edition-mot-de-passe/{id}', name: 'app_edit_password')]
public function editPassword(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $form = $this->createForm(UserPasswordType::class);

    $form->handleRequest($request);

    // Si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier le mot de passe actuel
        $currentPassword = $form->get('currentPassword')->getData();
        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            $form->get('currentPassword')->addError(new FormError('Le mot de passe actuel est incorrect.'));
            return $this->render('security/edit_password.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Hasher et mettre à jour le mot de passe
        $newPassword = $form->get('newPassword')->getData();
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        // Enregistrer l'utilisateur mis à jour
        $entityManager->persist($user);
        $entityManager->flush();

        // Rediriger ou ajouter un message de succès, selon vos besoins
        $this->addFlash('success', 'Le mot de passe a été modifié avec succès.');
        return $this->redirectToRoute('app_edit_password', ['id' => $user->getId()]);
    }

    // Si le formulaire n'est pas soumis, on affiche le formulaire
    return $this->render('security/edit_password.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/user/{id}/delete', name: 'app_delete_user')]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): Response
    {
        // Vérifier si l'utilisateur est connecté et s'il s'agit de son propre compte
        if ($this->getUser() && $this->getUser()->getId() === $user->getId()) {

            // Supprimer l'utilisateur directement par son ID
            $entityManager->remove($user);
            $entityManager->flush();

            // Déconnecter l'utilisateur après la suppression
            $tokenStorage->setToken(null);

            // Ajouter un message de succès
            $this->addFlash('success', 'Votre compte a bien été supprimé.');

            // Rediriger vers la page d'accueil
            return $this->redirectToRoute('app_home');
        } else {
            // Rediriger vers la page d'accueil si l'utilisateur n'est pas connecté ou s'il essaie de supprimer un autre compte
            return $this->redirectToRoute('app_home');
        }
    }

}
