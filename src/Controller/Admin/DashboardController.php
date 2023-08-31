<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Entity\ProjectTeam;
use App\Entity\Role;
use App\Entity\Team;
use App\Entity\TeamMember;
use App\Entity\User;
use App\Entity\Workplace;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        /** @var $routeBuilder AdminUrlGenerator */
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
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
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Godišnji Odmor');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Project', 'fas fa-home', Project::class);
        yield MenuItem::linkToCrud('ProjectTeam', 'fas fa-home', ProjectTeam::class);
        yield MenuItem::linkToCrud('Roles', 'fas fa-home', Role::class);
        yield MenuItem::linkToCrud('Team', 'fas fa-home', Team::class);
        yield MenuItem::linkToCrud('TeamMember', 'fas fa-home', TeamMember::class);
        yield MenuItem::linkToCrud('User', 'fas fa-home', User::class);
        yield MenuItem::linkToCrud('Workplace', 'fas fa-home', Workplace::class);

        // yield MenuItem::linkToCrud('VacationRequest', 'fas fa-home', VacationRequest::class);
        // yield MenuItem::linkToCrud('AnnualVacation', 'fas fa-home', AnnualVacation::class);
    }
}
