<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiEmployeeController extends AbstractController
{
    #[Route('/employees', name: 'app_api_employees')]
    #[Cache(public: true, maxage: 60, mustRevalidate: true)]
    public function employees(UserRepository $userRepository): Response
    {
        $employees = $userRepository->findAll();

        return $this->json($employees);
    }

    #[Route('/employee/teams/{id}', name: 'app_api_employee_teams')]
    #[Cache(public: true, maxage: 60, mustRevalidate: true)]
    public function employeeTeams(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new Exception("Employee not found");
        }

        return $this->json($employee->getTeams());
    }

    #[Route('/employee/projects/{id}', name: 'app_api_employee_projects')]
    #[Cache(public: true, maxage: 60, mustRevalidate: true)]
    public function employeeProjects(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new Exception("Employee not found");
        }

        return $this->json($employee->getProjects());
    }

    #[Route('/employee/vacation-requests/{id}', name: 'app_api_employee_vacation_requests')]
    #[Cache(public: true, maxage: 60, mustRevalidate: true)]
    public function employeeVacationRequests(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new Exception("Employee not found");
        }

        return $this->json($employee->getVacationRequests());
    }
}
