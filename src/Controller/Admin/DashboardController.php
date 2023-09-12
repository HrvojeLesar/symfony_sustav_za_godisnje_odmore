<?php

namespace App\Controller\Admin;

use App\Entity\AnnualVacation;
use App\Entity\Project;
use App\Entity\ProjectTeam;
use App\Entity\Team;
use App\Entity\TeamMember;
use App\Entity\User;
use App\Entity\VacationRequest;
use App\Entity\Workplace;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractDashboardController
{
    public function __construct(protected TranslatorInterface $translator)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        /** @var $routeBuilder AdminUrlGenerator */
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->translator->trans('admin.title'));
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud($this->translator->trans('admin.project'), 'fas fa-home', Project::class);
        yield MenuItem::linkToCrud($this->translator->trans('admin.project_team'), 'fas fa-home', ProjectTeam::class);
        yield MenuItem::linkToCrud($this->translator->trans('admin.team'), 'fas fa-home', Team::class);
        yield MenuItem::linkToCrud($this->translator->trans('admin.team_member'), 'fas fa-home', TeamMember::class);
        yield MenuItem::linkToCrud($this->translator->trans('admin.user'), 'fas fa-home', User::class);
        yield MenuItem::linkToCrud($this->translator->trans('admin.workplace'), 'fas fa-home', Workplace::class);

        yield MenuItem::linkToCrud($this->translator->trans('admin.annual_vacation'), 'fas fa-home', AnnualVacation::class);
        yield MenuItem::linkToCrud($this->translator->trans('admin.vacation_request'), 'fas fa-home', VacationRequest::class);
    }
}
