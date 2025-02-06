<?php

namespace App\Controller\Admin;

use App\Entity\Projets;
use App\Entity\Tache;
use App\Entity\User;
use App\Entity\Addresse;
use Doctrine\Persistence\Proxy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
                    ->setController(UserCrudController::class)
                    ->generateUrl();
        return $this->redirect($url);

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('FilRougeWBWM');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Gestion FilRouge');
        yield MenuItem::linkToDashboard('Menu', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::subMenu('Utilisateurs', 'fa fa-users', User::class)
        ->setSubItems([
            MenuItem::linkToCrud('Ajouter', 'fa fa-plus', User::class)
                ->setAction(Crud::PAGE_NEW), 
            MenuItem::linkToCrud('Visualiser', 'fa fa-eye', User::class)
        ]);

        yield MenuItem::section('Adresses');
        yield MenuItem::subMenu('Adresse', 'fa fa-users', Addresse::class)
        ->setSubItems([
            MenuItem::linkToCrud('Ajouter', 'fa fa-plus', Addresse::class)
                ->setAction(Crud::PAGE_NEW), 
            MenuItem::linkToCrud('Visualiser', 'fa fa-eye', Addresse::class)
        ]);


        yield MenuItem::section('Projets');
        yield MenuItem::subMenu('Projets', 'fa fa-users', Projets::class)
        ->setSubItems([
            MenuItem::linkToCrud('Ajouter', 'fa fa-plus', Projets::class)
                ->setAction(Crud::PAGE_NEW), 
            MenuItem::linkToCrud('Visualiser', 'fa fa-eye', Projets::class)
        ]);

        yield MenuItem::section('Taches');
        yield MenuItem::subMenu('Taches', 'fa fa-users', Tache::class)
            ->setSubItems([
                MenuItem::linkToCrud('Ajouter', 'fa fa-plus', Tache::class)
                    ->setAction(Crud::PAGE_NEW), 
                MenuItem::linkToCrud('Visualiser', 'fa fa-eye', Tache::class)
            ]);

    }
}
