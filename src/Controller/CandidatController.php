<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Formation;
use App\Form\CandidatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatController extends AbstractController
{
    // Méthode pour modifier le profil du candidat
    #[Route('/candidat/{id}/edit', name: 'app_candidat_edit')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
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

            $deletePhoto = $form->get('deletePhoto')->getData();

            $deleteCV = $form->get('deleteCV')->getData();

            $photo = $form->get('photo')->getData();

            $cv = $form->get('cv')->getData();

            if($deletePhoto) {
                // Suppression de l'ancienne photo du serveur
                unlink($this->getParameter('photo_profil').'/'.$user->getPhoto());
                // Suppression de la photo dans la BDD
                $user->setPhoto(null);
            }
            
            if($deleteCV) {
                // Suppression de l'ancien CV du serveur
                unlink($this->getParameter('cv_directory').'/'.$user->getCv());
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
