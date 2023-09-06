<?php

namespace App\Controller;

use App\Entity\AnnualVacation;
use App\Entity\VacationRequest;
use App\Entity\User;
use App\Form\VacationRequestFormType;
use App\Repository\AnnualVacationRepository;
use App\Repository\VacationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VacationRequestController extends AbstractController
{
    #[Route('/vacation/request', name: 'app_vacation_request')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vacationRequest = new VacationRequest();

        $form = $this->createForm(VacationRequestFormType::class, $vacationRequest);

        $form->handleRequest($request);

        /** @var AnnualVacationRepository $repo */
        $repo = $entityManager->getRepository(AnnualVacation::class);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();

            $annualVacation = $user->getLatestAnnualVacation();

            $vacationRequest->setUser($user);
            $vacationRequest->setAnnualVacation($annualVacation);
            $entityManager->persist($vacationRequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee');
        }

        return $this->render('vacation_request/index.html.twig', [
            'form' => $form,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/vacation/remove/{id}', name: 'app_vacation_request_remove')]
    public function removeVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $vacationRequestRepo->find($id);

        if (is_null($vacationRequest)) {
            throw new InvalidArgumentException("Provided id does not correspond to a VacationRequest");
        }

        if (! $vacationRequest->isRemovable()) {
            throw new Exception("Selected VacationRequest is not removable");
        }

        /** @var User $user */
        $user = $this->getUser();
        if ($vacationRequest->getUser()->getId() !== $user->getId()) {
            throw new Exception('User has no permissions for this action.');
        }

        $entityManager->remove($vacationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee');
    }

    #[Route('/vacation/project-lead-grant/{id}', name: 'app_vacation_request_project_lead_grant')]
    public function grantProjectVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $vacationRequestRepo->find($id);

        if (is_null($vacationRequest)) {
            throw new InvalidArgumentException("Provided id does not correspond to a VacationRequest");
        }

        $employee = $vacationRequest->getUser();
        $employeeProjects = $employee->getProjects();

        /** @var User $user */
        $user = $this->getUser();
        $granteeProjects = $user->leaderOfProjects();

        $projectMatch = array_unique(array_merge($employeeProjects, $granteeProjects), SORT_REGULAR);

        if (count($projectMatch) === 0) {
            throw new Exception('User has no permissions for this action.');
        }

        $vacationRequest->setIsApprovedByProjectLead(true);
        $vacationRequest->setApprovedByProjectLead($user);
        $entityManager->persist($vacationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }

    #[Route('/vacation/project-lead-reject/{id}', name: 'app_vacation_request_project_lead_reject')]
    public function rejectProjectVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $vacationRequestRepo->find($id);

        if (is_null($vacationRequest)) {
            throw new InvalidArgumentException("Provided id does not correspond to a VacationRequest");
        }

        $employee = $vacationRequest->getUser();
        $employeeProjects = $employee->getProjects();

        /** @var User $user */
        $user = $this->getUser();
        $granteeProjects = $user->leaderOfProjects();

        $projectMatch = array_unique(array_merge($employeeProjects, $granteeProjects), SORT_REGULAR);

        if (count($projectMatch) === 0) {
            throw new Exception('User has no permissions for this action.');
        }

        $vacationRequest->setIsApprovedByProjectLead(false);
        $vacationRequest->setApprovedByProjectLead($user);
        $entityManager->persist($vacationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }

    #[Route('/vacation/team-lead-grant/{id}', name: 'app_vacation_request_team_lead_grant')]
    public function grantTeamVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $vacationRequestRepo->find($id);

        if (is_null($vacationRequest)) {
            throw new InvalidArgumentException("Provided id does not correspond to a VacationRequest");
        }

        $employee = $vacationRequest->getUser();
        $employeeTeams = $employee->getTeams()->toArray();

        /** @var User $user */
        $user = $this->getUser();
        $granteeTeams = $user->leaderOfTeams();

        $teamMatch = array_unique(array_merge($employeeTeams, $granteeTeams), SORT_REGULAR);

        if (count($teamMatch) === 0) {
            throw new Exception('User has no permissions for this action.');
        }

        $vacationRequest->setIsApprovedByTeamLead(true);
        $vacationRequest->setApprovedByTeamLead($user);
        $entityManager->persist($vacationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }

    #[Route('/vacation/team-lead-reject/{id}', name: 'app_vacation_request_team_lead_reject')]
    public function rejectTeamVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $vacationRequestRepo->find($id);

        if (is_null($vacationRequest)) {
            throw new InvalidArgumentException("Provided id does not correspond to a VacationRequest");
        }

        $employee = $vacationRequest->getUser();
        $employeeTeams = $employee->getTeams()->toArray();

        /** @var User $user */
        $user = $this->getUser();
        $granteeTeams = $user->leaderOfTeams();

        $teamMatch = array_unique(array_merge($employeeTeams, $granteeTeams), SORT_REGULAR);

        if (count($teamMatch) === 0) {
            throw new Exception('User has no permissions for this action.');
        }

        $vacationRequest->setIsApprovedByTeamLead(false);
        $vacationRequest->setApprovedByTeamLead($user);
        $entityManager->persist($vacationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }
}
