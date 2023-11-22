<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Memory;
use App\Entity\Type;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator){
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        // $url = $this->adminUrlGenerator
        //     ->setController(MemoryCrudController::class)
        //     ->generateUrl();
        // return $this->redirect($url);
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Brain Dumper');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Accueil','fa fa-hom'),
            // MenuItem::linkToRoute('Liste des Users','fa-regular fa-check-square', 'app_memories'),
            MenuItem::linkToCrud('Memories', 'fas fa-list', Memory::class),
            MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class),
            MenuItem::linkToCrud('Types', 'fas fa-list', Type::class),
            MenuItem::linkToCrud('Users', 'fas fa-list', User::class)
        ];

        // // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}