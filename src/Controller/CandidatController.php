<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Emploi;
use App\Entity\Langue;
use App\Form\UserType;
use App\Entity\Postule;
use App\Entity\Formation;
use App\Form\CandidatType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/candidat')]
class CandidatController extends AbstractController
{
    // Méthode pour modifier le profil du candidat
    #[Route('/edit/{id}', name: 'app_candidat_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Si l'utilisateur n'est pas connecté
        if(!$this->getUser()) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_login');
        }

        // Si l'utilisateur courant est différent de l'utilisateur dont on veut modifier le profil
        if($this->getUser() !== $user) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(CandidatType::class, $user);

        $form->handleRequest($request);
        
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère les valeurs de certains champs
            $deletePhoto = $form->get('deletePhoto')->getData();
            $deleteCV = $form->get('deleteCV')->getData();
            $photo = $form->get('photo')->getData();
            $cv = $form->get('cv')->getData();
            $active = $form->get('active')->getData();
            $villes = $form->get('villes')->getData();
            $metiers = $form->get('metiers')->getData();
            $langues = $form->get('langues')->getData();
            $nom = $form->get('nom')->getData();
            $prenom = $form->get('prenom')->getData();
            $niveau = $form->get('niveau')->getData();

            if($deletePhoto) {
                // Suppression de l'ancienne photo du serveur
                unlink($this->getParameter('photo_profil').'/'.$user->getPhoto());
                // Suppression de la photo dans la BDD
                $user->setPhoto(null);
            }

            // Si l'utilisateur supprime le cv et coche l'activation du profil en même temps
            if($deleteCV && $active) {
                // Gestion de l'erreur
                $this->addFlash('error', "Vous ne pouvez pas activer votre profil sans CV");
                // Redirection
                return $this->redirectToRoute('app_candidat_edit', ['id' => $user->getId()]);
            }
            
            if($deleteCV) {
                // Suppression de l'ancien CV du serveur si il existe
                if($user->getCv()) {
                    unlink($this->getParameter('cv_directory').'/'.$user->getCv());
                }
                // Suppression du CV dans la BDD
                $user->setCv(null);
            }

            // this condition is needed because the 'photo' field is not required
            // so the file must be processed only when a file is uploaded
            if($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();
                
                try {
                    $photo->move(
                        $this->getParameter('photo_profil'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Suppression de l'ancienne photo du serveur si une nouvelle photo est uploadée
                if($user->getPhoto()) {
                    unlink($this->getParameter('photo_profil').'/'.$user->getPhoto());
                }

                // updates the 'photoFilename' property to store the PDF file name
                // instead of its contents
                $user->setPhoto($newFilename);
            }
            
            if($cv) {
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();
                
                try {
                    $cv->move(
                        $this->getParameter('cv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Suppression de l'ancien CV du serveur si un nouveau CV est uploadé
                if($user->getCv()) {
                    unlink($this->getParameter('cv_directory').'/'.$user->getCv());
                }

                // updates the 'cvFilename' property to store the PDF file name
                // instead of its contents
                $user->setCv($newFilename);
            }

            // Vérifie si l'utilisateur a coché l'activation du profil
            if($active) {
                    // Vérifie si les champs obligatoires sont renseignés
                    if(empty($user->getCv()) || empty($villes) || empty($metiers) || $langues->count() === 0 || empty($nom) || empty($prenom) || empty($niveau)) {
                        // Champ obligatoire non renseigné, gestion de l'erreur
                        $this->addFlash('error', "Veuillez renseigner tous les champs afin d'activer votre profil (la photo de profil n'est pas obligatoire)");
                        // Redirection
                        return $this->redirectToRoute('app_candidat_edit', ['id' => $user->getId()]);
                    }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_candidat_edit', ['id' => $user->getId()]);
        }
        
        // Si le formulaire n'est pas soumis, on affiche le formulaire
        return $this->render('candidat/profil.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // Méthode pour afficher la liste des candidatures envoyées et les emplois sauvegardés par le candidat
    #[Route('/liste', name: 'app_candidatures')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupère les candidatures envoyées par l'utilisateur
        $candidatures = $user->getPostulations();

        // Récupère les emplois sauvegardés par l'utilisateur
        $emplois = $user->getEmploiSauvegarder();

        return $this->render('candidat/liste_candidatures.html.twig', [
            'candidatures' => $candidatures,
            'emplois' => $emplois,
        ]);
    }

    // Méthode pour postuler
    #[Route('/postuler/emploi/{id}', name: 'app_postuler')]
    public function postuler(Emploi $emploi,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Vérifiez si l'utilisateur a déjà postulé à cet emploi
        $dejaPostuler = $entityManager->getRepository(Postule::class)
        ->findOneBy(['userPostulant' => $user, 'emploi' => $emploi]);

        // Si l'utilisateur a déjà postulé
        if ($dejaPostuler) {
            $this->addFlash('error', 'Vous avez déjà postulé à cet emploi.');
            return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
        }

        $candidature = new Postule();
        $candidature->setUserPostulant($user);
        $candidature->setEmploi($emploi);
        $candidature->setDatePostulation(new \DateTime());

        $entityManager->persist($candidature);
        $entityManager->flush();

        $this->addFlash('success', 'Votre candidature est envoyée');

        // Redirigez si tout ce passe bien
        return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);

    }

    // Méthode pour sauvegarder un emploi
    #[Route('/saveEmploi/{id}', name: 'app_emploi_save')]
    public function sauvegarderEmploi(Emploi $emploi,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        $user = $this->getUser();

        // Vérifier si l'utilisateur n'a pas déjà sauvegarder cet emploi
        $alreadySaved = $emploi->getUsers()->contains($user);
        
        // Si l'utilisateur a déjà sauvegarder cet emploi
        if ($alreadySaved) {
            $this->addFlash('error', 'Vous avez déjà sauvegarder cet emploi.');
            return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
        }

        $user->addEmploiSauvegarder($emploi);
        $emploi->addUser($user);

        $entityManager->persist($user);
        $entityManager->persist($emploi);

        $entityManager->flush();

        $this->addFlash('success', 'Emploi sauvegarder avec succès');

        // Redirigez si tout ce passe bien
        return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
    }

    // Méthode pour supprimer une candidature
    #[Route('/delete/candidature/{id}/{origin}', name: 'app_candidature_delete')]
    public function delete(Postule $candidature, string $origin, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Si la candidature n'existe pas
        if(!$candidature) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        // Si l'utilisateur courant est différent de l'utilisateur dont on veut supprimer la candidature
        if($this->getUser() !== $candidature->getUserPostulant()) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        // Suppression de la candidature
        $entityManager->remove($candidature);
        $entityManager->flush();

        // Message de succès
        $this->addFlash('success', 'Candidature retirer avec succès.');

        // On renvoi vers la page de la liste des candidatures
        if($origin === 'dashboard') {
            return $this->redirectToRoute('app_candidatures');
        } else if ($origin === 'detail') {
            return $this->redirectToRoute('app_show_emploi', ['id' => $candidature->getEmploi()->getId()]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    // Méthode pour supprimer un emploi sauvegardé
    #[Route('/delete/emploi-save/{id}/{origin}', name: 'app_emploi_delete')]
    public function deleteEmploiSauvegarder(Emploi $emploi, string $origin, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Si l'emploi n'existe pas
        if(!$emploi) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }
        
        // Si l'utilisateur courant n'appartient pas à la collection d'utilisateurs de l'emploi
        if (!$emploi->getUsers()->contains($user)) {
            // On renvoie vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        // On retire l'emploi de la liste des emplois sauvegardés par l'utilisateur
        $user->removeEmploiSauvegarder($emploi);

        // On enregistre les modifications
        $entityManager->flush();

        $this->addFlash('success', 'Emploi retirer des sauvegarder avec succès');

        // On renvoi vers la page de la liste des candidatures ou la page de détail de l'emploi en fonction de l'origine
        if ($origin === 'dashboard') {
            return $this->redirectToRoute('app_candidatures');
        } else if ($origin === 'detail') {
            return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
        } else if ($origin === 'resultats') {
            // Recharger juste la page actuelle
            return $this->redirect($request->headers->get('referer'));
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}
