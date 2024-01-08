<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Entreprise;
use App\Entity\Represente;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/recruteur/entreprise')]
class EntrepriseController extends AbstractController
{
    // Méthode pour afficher la liste des entreprises
    #[Route('/liste', name: 'app_entreprises')]
    public function liste(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupérer les entreprise que représente l'utilisateur / recruteur
        $entrepriseRepresenter = $user->getEntrepriseRepresenter();

        return $this->render('entreprise/index.html.twig', [
            'entrepriseRepresenter' => $entrepriseRepresenter,
        ]);
    }

    // Méthode pour ajouter / modifier une entreprise
    #[Route('/new', name: 'app_new_entreprise')]
    #[Route('/edit/{id}', name: 'app_edit_entreprise')]
    public function new_edit_entreprise(Entreprise $entreprise = null, Request $request,EntityManagerInterface $entityManager,SluggerInterface $slugger) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Création de l'entreprise et association de l'utilisateur en tant que représentant de cet entreprise
        if(!$entreprise) {

            $represantant = new Represente();
            $represantant->setStatus(1);
            $represantant->setUserEntreprise($user);

            $entreprise = new Entreprise();
            $entreprise->setUser($user);
            $entreprise->addRepresentant($represantant);
        }

        // Vérfie si l'entreprise appartient à l'utilisateur connecté
        $hasAccess = $entreprise->getUser() === $this->getUser();
        
        // Vérifie si l'utilisateur connecté a le droit de modifier l'entreprise
        if(!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de modifier cet entreprise.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifie si l'entreprise existe
        if(!$entreprise) {
            $this->addFlash('danger', 'L\'entreprise n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(EntrepriseType::class, $entreprise);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $logo = $form->get('logo')->getData();

            $banniere = $form->get('banniere')->getData();

            $kbis = $form->get('kbis')->getData();

            // this condition is needed because the 'photo' field is not required
            // so the file must be processed only when a file is uploaded
            if($logo) {
                $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logo->guessExtension();
                
                try {
                    $logo->move(
                        $this->getParameter('logo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Suppression de l'ancien logo du serveur si un nouveau logo est uploadée
                if($entreprise->getLogo()) {
                    unlink($this->getParameter('logo_directory').'/'.$entreprise->getLogo());
                }

                // updates the 'photoFilename' property to store the PDF file name
                // instead of its contents
                $entreprise->setLogo($newFilename);
            }

            if($banniere) {
                $originalFilename = pathinfo($banniere->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$banniere->guessExtension();
                
                try {
                    $banniere->move(
                        $this->getParameter('logo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Suppression de l'ancienne banniere du serveur si une nouvelle banniere est uploadée
                if($entreprise->getBanniere()) {
                    unlink($this->getParameter('logo_directory').'/'.$entreprise->getBanniere());
                }

                // updates the 'photoFilename' property to store the PDF file name
                // instead of its contents
                $entreprise->setBanniere($newFilename);
            }
            
            if($kbis) {
                $originalFilename = pathinfo($kbis->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$kbis->guessExtension();
                
                try {
                    $kbis->move(
                        $this->getParameter('kbis_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Suppression de l'ancien KBIS du serveur si un nouveau KBIS est uploadé
                if($entreprise->getKbis()) {
                    unlink($this->getParameter('kbis_directory').'/'.$entreprise->getKbis());
                }

                // updates the 'kbisFilename' property to store the PDF file name
                // instead of its contents
                $entreprise->setKbis($newFilename);
            }

            // Message dans le cas ou l'entreprise vient d'être ajoutée
            $message = 'L\'Entreprise a bien été ajoutée, elle est en cours de vérification.';

            // Dans le cas ou le recruteur demande une réexamination de l'entreprise
            // On remet le statut de l'entreprise à NULL (en attente de vérification)
            if($entreprise->isIsVerified() == 0) {
                $entreprise->setIsVerified(NULL);
                $entityManager->flush();
                // Message dans le cas ou il s'agit d'une réexamination
                $message = 'La demande de réexamination de l\'entreprise a bien été envoyée.';
            } elseif ($entreprise->getId()) {
                // Message dans le cas ou l'entreprise vient d'être modifiée
                $message = 'L\'Entreprise a bien été modifiée.';
            }

            $entityManager->persist($entreprise);
            $entityManager->flush();

            $this->addFlash('success', $message);

            return $this->redirectToRoute('app_entreprises');
        }

        return $this->render('entreprise/new.html.twig', [
            'form' => $form,
            'edit' => $entreprise->getId(),
        ]);
    }

    // Méthode pour supprimer une entreprise
    #[Route('/delete/{id}', name: 'app_delete_entreprise')]
    public function delete_entreprise(Entreprise $entreprise, EntityManagerInterface $entityManager) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        $hasAccess = $entreprise->getUser() === $this->getUser();

        if(!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de supprimer cet entreprise.');
            return $this->redirectToRoute('app_home');
        }

        if(!$entreprise) {
            $this->addFlash('danger', 'L\'entreprise n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
        $entityManager->remove($entreprise);

        // Suppression du logo du serveur
        if($entreprise->getLogo()) {
            unlink($this->getParameter('logo_directory').'/'.$entreprise->getLogo());
        }

        // Suppression de la banniere du serveur
        if($entreprise->getBanniere()) {
            unlink($this->getParameter('logo_directory').'/'.$entreprise->getBanniere());
        }

        // Suppression du KBIS du serveur
        if($entreprise->getKbis()) {
            unlink($this->getParameter('kbis_directory').'/'.$entreprise->getKbis());
        }

        $entityManager->flush();

        // Ajoute un message de succès
        $this->addFlash('success', 'L\'entreprise a été supprimé avec succès.');

        return $this->redirectToRoute('app_entreprises');
    }
}
