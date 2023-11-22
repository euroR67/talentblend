<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RepresenteController extends AbstractController
{
    #[Route('/represente', name: 'app_represente')]
    public function index(): Response
    {
        return $this->render('represente/index.html.twig', [
            'controller_name' => 'RepresenteController',
        ]);
    }
}
