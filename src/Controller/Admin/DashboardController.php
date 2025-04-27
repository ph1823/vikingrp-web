<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\WikiCategory;
use App\Entity\WikiPage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/home.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('VikingRP Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Article', 'fa-solid fa-newspaper', Article::class);
        yield MenuItem::linkToCrud('WikiPage', 'fa-solid fa-newspaper', WikiPage::class);
        yield MenuItem::linkToCrud('WikiCat', 'fa-solid fa-newspaper', WikiCategory::class);
    }
}
