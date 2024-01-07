<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Ville;
use App\Entity\Emploi;
use App\Entity\Langue;
use App\Entity\Metier;
use App\Entity\Niveau;
use App\Entity\Taille;
use App\Entity\Contrat;
use App\Entity\Categorie;
use App\Entity\Entreprise;
use App\Entity\Represente;
use App\Entity\TypeEmploi;
use App\Controller\Admin\UserCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Locale;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/img/logo.png" style="height: 45px;">');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Entreprises', 'fas fa-building', Entreprise::class);
        yield MenuItem::linkToCrud('Representants', 'fas fa-user-tie', Represente::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-box', Categorie::class);
        yield MenuItem::linkToCrud('Emplois', 'fas fa-box', Emploi::class);
        yield MenuItem::linkToCrud('Langues', 'fas fa-box', Langue::class);
        yield MenuItem::linkToCrud('Metiers', 'fas fa-box', Metier::class);
        yield MenuItem::linkToCrud('Niveaux', 'fas fa-box', Niveau::class);
        yield MenuItem::linkToCrud('Tailles', 'fas fa-box', Taille::class);
        yield MenuItem::linkToCrud('Types Emplois', 'fas fa-box', TypeEmploi::class);
        yield MenuItem::linkToCrud('Contrats', 'fas fa-box', Contrat::class);
        yield MenuItem::linkToCrud('Villes', 'fas fa-box', Ville::class);
    }
}
