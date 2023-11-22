<?php

namespace App\Controller;

use App\Entity\Represente;
use App\Form\RepresenteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/recruteur/represente')]
class RepresenteController extends AbstractController
{
    // Méthode pour représenter une entreprise ( ajout / réexamination )
    #[Route('/new', name: 'app_new_represente')]
    #[Route('/reverify/{id}', name: 'app_reverify_represente')]
    public function new_reverify(Represente $represente = null,Request $request,EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUTEUR');

        // Récupère l'utilisateur en session
        $user = $this->getUser();

        if(!$represente) {

            $represente = new Represente();
            $represente->setUserEntreprise($user);

        }

        // Vérfie si represente appartient à l'utilisateur connecté
        $hasAccess = $represente->getUserEntreprise() === $this->getUser();
        
        // Vérifie si l'utilisateur connecté a le droit de demander une réexamination
        if(!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas le droit de demander une réexamination.');
            return $this->redirectToRoute('app_home');
        }

        // Vérifie si la representation n'existe pas
        if(!$represente) {
            $this->addFlash('danger', 'la representation n\'existe pas');
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RepresenteType::class, $represente);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $kbis = $form->get('kbis')->getData();

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
                if($represente->getKbis()) {
                    unlink($this->getParameter('kbis_directory').'/'.$represente->getKbis());
                }

                // updates the 'kbisFilename' property to store the PDF file name
                // instead of its contents
                $represente->setKbis($newFilename);
            }

            $entityManager->persist($represente);

            $entityManager->flush();

            return $this->redirectToRoute('app_entreprises');
        }


        return $this->render('represente/new.html.twig', [
            'form' => $form,
            'reverify' => $represente->getId(),
        ]);
    }
}
