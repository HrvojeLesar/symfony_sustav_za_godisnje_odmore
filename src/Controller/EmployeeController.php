<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\VacationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employee', name: 'app_employee')]
class EmployeeController extends AbstractController
{
    #[Route('', name: '', methods: 'GET')]
    public function index(TeamRepository $teamRepository): Response
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
    public function checkVacationRequests(EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $teamLeadVacationRequests = $vacationRequestRepository->getPendingTeamLeadVacationRequests($user);
        $projectLeadVacationRequests = $vacationRequestRepository->getPendingProjectLeadVacationRequests($user);
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

    #[Route('/employees.csv', name: '_employees_csv', methods: 'GET')]
    public function employeesCSV(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $data = [User::getCSVHeader()];
        foreach($users as &$user) {
            $data[] = $user->toCSV();
        }
        $dataStringified = implode("\n", $data);

        return new Response($dataStringified, Response::HTTP_OK, ['Content-Type' => 'text/csv']);
    }

    #[Route('/employees.pdf', name: '_employees_pdf', methods: 'GET')]
    public function employeesPDF(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $renderedView = $this->renderView('pdf/users.html.twig', [
            'users' => $users
        ]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($renderedView);
        $dompdf->render();

        return new Response($dompdf->stream('resume', ['Attachment' => false]), Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
    }
}
