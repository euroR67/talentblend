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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/candidat')]
class CandidatController extends AbstractController
{
    // Méthode pour modifier le profil du candidat
    #[Route('/edit/{id}', name: 'app_candidat_edit')]
    public function edit(Request $request, User $user = null, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Si l'utilisateur n'existe pas
        if(!$user) {
            // On renvoi vers la page d'erreur 404
            return $this->redirectToRoute('app_error404');
        }

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

        // Utilisation du composant Form pour créer le formulaire
        $form = $this->createForm(CandidatType::class, $user);

        // Gérez la soumission du formulaire
        $form->handleRequest($request);
        
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère les valeurs de certains champs
            $deletePhoto = $form->get('deletePhoto')->getData();
            $deleteCV = $form->get('deleteCV')->getData();
            $photo = $form->get('photo')->getData();
            $cv = $form->get('cv')->getData();
            $active = $form->get('active')->getData();
            $ville = $form->get('ville')->getData();
            $metier = $form->get('metier')->getData();
            $langues = $form->get('langues')->getData();
            $nom = $form->get('nom')->getData();
            $prenom = $form->get('prenom')->getData();
            $niveau = $form->get('niveau')->getData();
            $description = $form->get('description')->getData();
            $formations = $form->get('formations')->getData();
            $experiences = $form->get('experiences')->getData();
            $typesEmploi = $form->get('typesEmploi')->getData();
            $contrats = $form->get('contrats')->getData();
            

            // Si la date de fin de formation est inférieur à la date de début
            foreach($formations as $formation) {
                if($formation->getDateFin() < $formation->getDateDebut()) {
                    // Gestion de l'erreur
                    $this->addFlash('error', "La date de fin de la formation ".$formation->getTitre()." ne peut pas être inférieur à la date de début");
                    // Redirection
                    return $this->redirectToRoute('app_candidat_edit', ['id' => $user->getId()]);
                }
            }
            
            // Si la date de fin d'expérience est inférieur à la date de début
            foreach($experiences as $experience) {
                if($experience->getDateFin() < $experience->getDateDebut()) {
                    // Gestion de l'erreur
                    $this->addFlash('error', "La date de fin de l'expérience ".$experience->getTitre()." ne peut pas être inférieur à la date de début");
                    // Redirection
                    return $this->redirectToRoute('app_candidat_edit', ['id' => $user->getId()]);
                }
            }

            if($deletePhoto) {
                // Suppression de l'ancienne photo du serveur si elle existe
                $photoPath = $this->getParameter('photo_profil').'/'.$user->getPhoto();
                if($user->getPhoto() && file_exists($photoPath)) {
                    unlink($photoPath);
                }
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
                $cvPath = $this->getParameter('cv_directory').'/'.$user->getCv();
                if($user->getCv() && file_exists($cvPath)) {
                    unlink($cvPath);
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
                // Utilisation de md5 pour générer un nom de fichier unique et ajout de l'extension .webp
                $newFilename = md5($safeFilename.'-'.uniqid()).'.webp';
                
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
                    $file_path = $this->getParameter('photo_profil').'/'.$user->getPhoto();
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            
                // updates the 'photoFilename' property to store the PDF file name
                // instead of its contents
                $user->setPhoto($newFilename);
            }
            
            if($cv) {
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                // Utilisation de md5 pour générer un nom de fichier unique et ajout de l'extension .pdf
                $newFilename = md5($safeFilename.'-'.uniqid()).'.pdf';
                
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
                    $file_path = $this->getParameter('cv_directory').'/'.$user->getCv();
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            
                // updates the 'cvFilename' property to store the PDF file name
                // instead of its contents
                $user->setCv($newFilename);
            }
            
            // Vérifie si l'utilisateur a coché l'activation du profil
            if($active) {
                
                // Vérifie si les champs obligatoires sont renseignés
                if( $langues->count() === 0
                || empty($user->getCv())
                || empty($ville)
                || empty($metier)
                || empty($nom)
                || empty($prenom)
                || empty($niveau)
                || $contrats->count() === 0
                || $typesEmploi->count() === 0
                || empty($description)) {
                    // Champ obligatoire non renseigné, gestion de l'erreur
                    $this->addFlash('error', "Veuillez renseigner tous les champs afin d'activer votre profil (la photo de profil n'est pas obligatoire)");
                    // Redirection
                    return $this->redirectToRoute('app_candidat_edit', ['id' => $user->getId()]);
                }
            }

            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Profil mis à jour avec succès.');

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
    public function postuler(Emploi $emploi = null,Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Si l'emploi n'existe pas
        if(!$emploi) {
            // On renvoi vers la page d'erreur 404
            return $this->redirectToRoute('app_error404');
        }

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

    // Méthode pour postuler avec un CV
    #[Route('/postulerAvecCurriculum/emploi/{id}', name: 'app_postuler_cv', methods: ['POST'])]
    public function postulerAvecCv(Emploi $emploi = null, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Si l'emploi n'existe pas
        if(!$emploi) {
            // On renvoi vers la page d'erreur 404
            return $this->redirectToRoute('app_error404');
        }

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

        // Si le CV n'est pas uploadé
        if (!$request->files->has('cv')) {
            $this->addFlash('error', 'Veuillez sélectionner un CV.');
            return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
        }

        $cv = $request->files->get('cv');
        
        // Récupère le message si il existe
        if($request->request->get('message')) {
            $message = $request->request->get('message');
        } else {
            $message = null;
        }

        // Vérifie si le fichier est bien un PDF
        if($cv->getMimeType() !== 'application/pdf') {
            $this->addFlash('error', 'Veuillez sélectionner un fichier PDF.');
            return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
        }

        // Vérifie si le fichier ne dépasse pas 5Mo
        if($cv->getSize() > 5000000) {
            $this->addFlash('error', 'Le fichier ne doit pas dépasser 5Mo.');
            return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
        }

        // dd($message, $cv);

        $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $slugger->slug($originalFilename);
        // Utilisation de md5 pour générer un nom de fichier unique et ajout de l'extension .pdf
        $newFilename = md5($safeFilename.'-'.uniqid()).'.pdf';
        
        try {
            $cv->move(
                $this->getParameter('cv_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
            $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de votre candidature.');
        }
    
        // Suppression de l'ancien CV du serveur si un nouveau CV est uploadé
        if($user->getCv()) {
            $file_path = $this->getParameter('cv_directory').'/'.$user->getCv();
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $candidature = new Postule();
        $candidature->setCv($newFilename);
        $candidature->setMessage($message);
        $candidature->setUserPostulant($user);
        $candidature->setEmploi($emploi);
        $candidature->setDatePostulation(new \DateTime());

        $entityManager->persist($candidature);
        $entityManager->flush();

        $this->addFlash('success', 'Votre candidature est envoyée');

        // Redirigez si tout ce passe bien
        return $this->redirectToRoute('app_show_emploi', ['id' => $emploi->getId()]);
    }

    // Méthode pour supprimer une candidature avec un CV et un message
    #[Route('/delete/candidatureAvecCurriculum/{id}/{origin}', name: 'app_candidature_cv_delete')]
    public function deleteCandidatureCv(Postule $candidature, string $origin, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Si la candidature n'existe pas
        if(!$candidature) {
            // On renvoi vers la page d'erreur 404
            return $this->redirectToRoute('app_error404');
        }

        // Si l'utilisateur courant est différent de l'utilisateur dont on veut supprimer la candidature
        if($this->getUser() !== $candidature->getUserPostulant()) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }

        // Suppression du CV de la candidature si il existe
        $file_path = $this->getParameter('cv_directory').'/'.$candidature->getCv();
        if ($candidature->getCv() && file_exists($file_path)) {
            unlink($file_path);
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

    // Méthode pour sauvegarder un emploi
    #[Route('/saveEmploi/{id}', name: 'app_emploi_save')]
    public function sauvegarderEmploi(Emploi $emploi = null,Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Si l'emploi n'existe pas
        if(!$emploi) {
            return new JsonResponse(['error' => 'Emploi non trouvé.'], 404);
        }

        $user = $this->getUser();

        // Vérifier si l'utilisateur n'a pas déjà sauvegarder cet emploi
        $alreadySaved = $emploi->getUsers()->contains($user);
        
        // Si l'utilisateur a déjà sauvegarder cet emploi
        if ($alreadySaved) {
            return new JsonResponse(['error' => 'Vous avez déjà sauvegarder cet emploi.'], 400);
        }

        $user->addEmploiSauvegarder($emploi);
        $emploi->addUser($user);

        $entityManager->persist($user);
        $entityManager->persist($emploi);

        $entityManager->flush();

        return new JsonResponse(['success' => 'Emploi mis en favoris avec succès']);
    }

    // Méthode pour supprimer un emploi sauvegardé
    #[Route('/deleteEmploi/{id}', name: 'app_emploi_delete')]
    public function deleteEmploiSauvegarder(Emploi $emploi, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Si l'emploi n'existe pas
        if(!$emploi) {
            return new JsonResponse(['error' => 'Emploi non trouvé.'], 404);
        }
        
        // Si l'utilisateur courant n'appartient pas à la collection d'utilisateurs de l'emploi
        if (!$emploi->getUsers()->contains($user)) {
            return new JsonResponse(['error' => 'Vous n\'avez pas sauvegardé cet emploi.'], 400);
        }

        // On retire l'emploi de la liste des emplois sauvegardés par l'utilisateur
        $user->removeEmploiSauvegarder($emploi);

        // On enregistre les modifications
        $entityManager->flush();

        return new JsonResponse(['success' => 'Emploi retiré des favoris avec succès']);
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

    // Méthode pour voir le profil du candidat
    #[Route('/profil_candidat/{id}', name: 'app_show_profil')]
    public function showCandidat($id,Request $request, EntityManagerInterface $entityManager, UserRepository $ur): Response
    {
        // Si l'utilisateur n'existe pas
        if(!$ur->find($id)) {
            // On renvoi vers la page 404
            return $this->redirectToRoute('app_error404');
        }

        // Récupère l'utilisateur en session
        $user = $this->getUser();
        
        // Accès réservé au candidat lui-même uniquement
        if($user->getId() != $id) {
            // On renvoi vers la page d'accueil
            return $this->redirectToRoute('app_home');
        }


        // Récupère le candidat en question
        $candidat = $ur->find($id);

        return $this->render('candidat/show_profil.html.twig', [
            'candidat' => $candidat,
        ]);
    }
}
