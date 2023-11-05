<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\CandidatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CandidatType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            $deletePhoto = $form->get('deletePhoto')->getData();

            $photo = $form->get('photo')->getData();

            if($deletePhoto) {
                // Suppression de l'ancienne photo du serveur
                unlink($this->getParameter('photo_profil').'/'.$user->getPhoto());
                // Suppression de la photo dans la BDD
                $user->setPhoto(null);
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

            // updates the 'photoFilename' property to store the PDF file name
            // instead of its contents
            $user->setPhoto($newFilename);

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
