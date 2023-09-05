<?php

namespace App\Controller;

use App\Entity\User;
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
        $rec = $user->getAnnualVacations();

        return $this->render('employee/index.html.twig', [
            'controller_name' => 'EmployeeController',
            'vacation_requests' => $vacationRequests,
        ]);
    }
}
