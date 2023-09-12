<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TeamRepository;
use App\Repository\VacationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employee', name: 'app_employee')]
class EmployeeController extends AbstractController
{
    #[Route('', name: '', methods: 'GET')]
    public function index(TeamRepository $tr): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $vacationRequests = $user->getVacationRequests();

        return $this->render('employee/index.html.twig', [
            'vacation_requests' => $vacationRequests,
            'user' => $user
        ]);
    }

    #[Route('/check-vacation-requests', name: '_check_vacation_requests', methods: 'GET')]
    public function checkVacationRequests(EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $teamLeadVacationRequests = $vacationRequestRepo->getPendingTeamLeadVacationRequests($user);
        $projectLeadVacationRequests = $vacationRequestRepo->getPendingProjectLeadVacationRequests($user);
        $teams = $user->getTeams();
        $projects = $user->leaderOfProjects();

        return $this->render('employee/check.html.twig', [
            'vacation_requests_team' => $teamLeadVacationRequests,
            'vacation_requests_project' => $projectLeadVacationRequests,
            'leader_of_teams' => $teams,
            'leader_of_projects' => $projects,
            'user' => $user
        ]);
    }

}
