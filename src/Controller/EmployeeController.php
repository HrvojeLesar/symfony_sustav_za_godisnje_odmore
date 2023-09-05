<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\VacationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $vacationRequests = $user->getVacationRequests();

        return $this->render('employee/index.html.twig', [
            'vacation_requests' => $vacationRequests,
            // 'vacation_requests' => $vacationRequests,
            'user' => $user
        ]);
    }

    #[Route('/employee/check-vacation-requests', name: 'app_employee_check_vacation_requests')]
    public function checkVacationRequests(EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $teamLeadVacationRequests = $vacationRequestRepo->getPendingTeamLeadVacationRequests($user);
        $projectLeadVacationRequests = $vacationRequestRepo->getPendingProjectLeadVacationRequests($user);

        return $this->render('employee/index.html.twig', [
            'vacation_requests' => $teamLeadVacationRequests,
            // 'vacation_requests' => $vacationRequests,
            'user' => $user
        ]);
    }

}
