<?php

namespace App\Controller;

use App\Entity\Emploi;
use App\Form\SearchEmploiType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_search')]
    public function search(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SearchEmploiType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $searchInfo = sprintf('%s - %s', $data['poste'], $data['ville']);

            // Utilisez Doctrine pour effectuer la recherche en fonction du poste et de la ville
            $results = $entityManager->getRepository(Emploi::class)
                ->searchByPosteAndVille($data['poste'], $data['ville']);

            return $this->render('emploi/emploi_results.html.twig', [
                'results' => $results,
                'searchInfo' => $searchInfo,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
