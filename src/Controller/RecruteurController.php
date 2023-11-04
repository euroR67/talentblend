<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecruteurController extends AbstractController
{
    #[Route('/recruteur', name: 'app_recruteur')]
    public function index(): Response
    {
        return $this->render('recruteur/index.html.twig', [
            'controller_name' => 'RecruteurController',
        ]);
    }
}
