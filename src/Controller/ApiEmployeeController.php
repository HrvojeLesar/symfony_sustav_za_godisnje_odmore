<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'app_api')]
class ApiEmployeeController extends AbstractController
{
    #[Route('/employees', name: '_employees', methods: 'GET')]
    public function employees(UserRepository $userRepository): Response
    {
        $employees = $userRepository->getAllUsersCached();

        return $this->json($employees);
    }

    #[Route('/employee/{id}/teams', name: '_employee_teams', methods: 'GET')]
    public function employeeTeams(int $id, UserRepository $userRepository): Response
    {
        $teams = $userRepository->getUserTeamsCached($id);

        return $this->json($teams);
    }

    #[Route('/employee/{id}/projects', name: '_employee_projects', methods: 'GET')]
    public function employeeProjects(int $id, UserRepository $userRepository): Response
    {
        $projects = $userRepository->getUserProjectsCached($id);

        return $this->json($projects);
    }

    #[Route('/employee/{id}/vacation-requests', name: '_employee_vacation_requests', methods: 'GET')]
    public function employeeVacationRequests(int $id, UserRepository $userRepository): Response
    {
        $vacationRequests = $userRepository->getUserVacationRequestsCached($id);

        return $this->json($vacationRequests);
    }
}
