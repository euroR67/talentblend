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
    #[Route('/liste', name: 'app_entreprises')]
    public function liste(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        // Récupérer les entreprise qui sont vérifier que représente l'utilisateur / recruteur
        $entrepriseRepresenter = $user->getEntrepriseRepresenter()->filter(function ($represente) {
            return $represente->getEntreprise()->IsIsVerified();
        });

        // Récupérer les entreprise non vérifier que représente l'utilisateur / recruteur
        $entrepriseRepresenterNonVerifie = $user->getEntrepriseRepresenter()->filter(function ($represente) {
            return !$represente->getEntreprise()->IsIsVerified();
        });

        // Pour chaque entreprise, compter le nombre d'emplois créés par l'utilisateur
        $emploisParEntreprise = [];
        foreach ($entrepriseRepresenter as $entreprise) {
            $emplois = $entreprise->getEntreprise()->getEmplois()->toArray();
            $emploisParEntreprise[$entreprise->getId()] = count(array_filter($emplois, function($emploi) use ($user) {
                return $emploi->getUser() === $user;
            }));
        }

        return $this->render('entreprise/index.html.twig', [
            'entrepriseRepresenter' => $entrepriseRepresenter,
            'emploisParEntreprise' => $emploisParEntreprise,
            'entrepriseRepresenterNonVerifie' => $entrepriseRepresenterNonVerifie,
        ]);
    }

    // Méthode pour ajouter / supprimer un entreprise
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
            $entreprise->setIsVerified(0);
            $entreprise->addRepresentant($represantant);
        }

        // Vérfie si l'entreprise appartient à l'utilisateur connecté
        $hasAccess = $entreprise->getUser() === $this->getUser();
        
        // Vérifie si l'utilisateur connecté a le droit de modifier l'entreprise
        if(!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de modifier cet entreprise.');
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

            $entityManager->persist($entreprise);

            $entityManager->flush();

            $this->addFlash('success', 'L\'Entreprise a bien été ajouter, il est en cours de vérification.');

            return $this->redirectToRoute('app_entreprises');
        }

        return $this->render('entreprise/new.html.twig', [
            'form' => $form,
            'edit' => $entreprise->getId(),
        ]);
    }
}
