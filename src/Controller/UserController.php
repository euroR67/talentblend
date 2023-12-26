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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class UserController extends AbstractController
{

    // Méthode pour télécharger le CV d'un utilisateur
    public function downloadCV(int $userId, EntityManagerInterface $entityManager): Response
    {
        // Récupérez l'utilisateur à partir de l'ID
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Vérifiez si la propriété cv de l'utilisateur est vide
        if (empty($user->getCv())) {
            return new Response('CV non trouvé', 404);
        }

        // Récupérez le chemin du CV
        $cvPath = $this->getParameter('cv_directory') . '/' . $user->getCv();

        // Créez une réponse pour le téléchargement du fichier
        $response = new BinaryFileResponse($cvPath);

        // Configurez le nom du fichier pour le téléchargement
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'cv_' . $user->getNom() . '_' . $user->getPrenom() . '.pdf'
        );

        return $response;
    }

    // Méthode pour permettre la modification du mot de passe de l'utilisateur
    #[Route('/user/edition-mot-de-passe', name: 'app_edit_password')]
    public function editPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            throw new AccessDeniedHttpException('Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer l'utilisateur à partir de l'ID
        $user = $this->getUser();

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

    // Méthode pour supprimer un utilisateur
    #[Route('/user/delete', name: 'app_delete_user')]
    public function deleteUser(Request $request, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userId = (int) $request->request->get('userId');
        $user = $entityManager->getRepository(User::class)->find($userId);

        // Vérifier si l'utilisateur est connecté et s'il s'agit de son propre compte
        if ($this->getUser() && $this->getUser()->getId() === $userId) {
            // Vérifier le mot de passe
            $submittedPassword = $request->request->get('password');
            if ($passwordHasher->isPasswordValid($user, $submittedPassword)) {
                // Supprimer le CV de l'utilisateur s'il existe
                if (!empty($user->getCv())) {
                    $cvPath = $this->getParameter('cv_directory') . '/' . $user->getCv();
                    unlink($cvPath);
                }

                // Supprimer l'image de profil de l'utilisateur s'il existe
                if (!empty($user->getPhoto())) {
                    $photoPath = $this->getParameter('photo_profil') . '/' . $user->getPhoto();
                    unlink($photoPath);
                }

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
                // Ajouter un message d'erreur
                $this->addFlash('error', 'Le mot de passe est incorrect.');
            }
        }
        // Handle other cases or return a response if needed
        return $this->redirectToRoute('app_edit_password');
    }

}
