<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiEmployeeController extends AbstractController
{
    #[Route('/api/employees', name: 'app_api_employees')]
    public function employees(UserRepository $userRepository): Response
    {
        $employees = $userRepository->findAll();

        return $this->json($employees);
    }

    #[Route('/api/employee/teams/{id}', name: 'app_api_employee_teams')]
    public function employeeTeams(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new Exception("Employee not found");
        }

        return $this->json($employee->getTeams());
    }

    #[Route('/api/employee/projects/{id}', name: 'app_api_employee_projects')]
    public function employeeProjects(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new Exception("Employee not found");
        }

        return $this->json($employee->getProjects());
    }

    #[Route('/api/employee/vacation-requests/{id}', name: 'app_api_employee_vacation_requests')]
    public function employeeVacationRequests(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new Exception("Employee not found");
        }

        return $this->json($employee->getVacationRequests());
    }
}
