<?php

namespace App\Controller\Admin;

use App\Entity\{ Comment, Task, User };
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;

class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Guestbook');
    }

    public function configureMenuItems(): iterable
    {
//        yield MenuItem::linkToRoute('Главная', 'fas fa-home', 'admin');
        yield MenuItem::linkToCrud('Task', 'fas fa-list', Task::class);
        yield MenuItem::linkToCrud('Comment', 'fas fa-list', Comment::class);
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
    }

//    public function configureActions(): Actions
//    {
//        return parent::configureActions()
//            ->add(Crud::PAGE_INDEX, Action::DETAIL);
//    }
//
//    public function configureAssets(): Assets
//    {
//        return parent::configureAssets()->addWebpackEncoreEntry('admin');
//    }
}
