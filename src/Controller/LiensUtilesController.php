<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/liens-utiles')]
class LiensUtilesController extends AbstractController
{
    // Route pour la page mentions légales
    #[Route('/mentiens-legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('liens_utiles/mentions_legales.html.twig');
    }

    // Route pour la page conditions d'utilisation
    #[Route('/conditions-utilisation', name: 'conditions_utilisation')]
    public function conditionsUtilisation(): Response
    {
        return $this->render('liens_utiles/conditions_utilisation.html.twig');
    }

    // Route pour la page données personnelles
    #[Route('/donnees-personnelles', name: 'donnees_personnelles')]
    public function donneesPersonnelles(): Response
    {
        return $this->render('liens_utiles/donnees_personnelles.html.twig');
    }

    // Route pour la page accessibilité
    #[Route('/accessibilite', name: 'accessibilite')]
    public function accessibilite(): Response
    {
        return $this->render('liens_utiles/accessibilite.html.twig');
    }

    // Route pour la page security
    #[Route('/security', name: 'security')]
    public function security(): Response
    {
        return $this->render('liens_utiles/security.html.twig');
    }

}
