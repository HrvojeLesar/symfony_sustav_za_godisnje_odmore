<?php

namespace App\Controller;

use App\Exceptions\EmployeeNotFoundException;
use App\Repository\UserRepository;
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

    #[Route('/employee/{id}/teams', name: '_employee_teams', methods: 'GET')]
    #[Cache(public: true, maxage: 60, mustRevalidate: true)]
    public function employeeTeams(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new EmployeeNotFoundException();
        }

        return $this->json($employee->getTeams());
    }

    #[Route('/employee/{id}/projects', name: '_employee_projects', methods: 'GET')]
    #[Cache(public: true, maxage: 60, mustRevalidate: true)]
    public function employeeProjects(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new EmployeeNotFoundException();
        }

        return $this->json($employee->getProjects());
    }

    #[Route('/employee/{id}/vacation-requests', name: '_employee_vacation_requests', methods: 'GET')]
    #[Cache(public: true, maxage: 60, mustRevalidate: true)]
    public function employeeVacationRequests(int $id, UserRepository $userRepository): Response
    {
        $employee = $userRepository->find($id);

        if (is_null($employee)) {
            throw new EmployeeNotFoundException();
        }

        return $this->json($employee->getVacationRequests());
    }
}
