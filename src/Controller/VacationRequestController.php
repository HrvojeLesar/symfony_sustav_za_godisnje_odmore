<?php

namespace App\Controller;

use App\Entity\AnnualVacation;
use App\Entity\VacationRequest;
use App\Entity\User;
use App\Exceptions\InvalidPermissionsException;
use App\Exceptions\NotRemovableException;
use App\Form\VacationRequestFormType;
use App\Message\ApprovalNotification;
use App\Repository\AnnualVacationRepository;
use App\Repository\VacationRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vacation', name: 'app_vacation')]
class VacationRequestController extends AbstractController
{
    #[Route('/request', name: '_request', methods: ['GET', 'POST'])]
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

    #[Route('/remove/{id}', name: '_request_remove', methods: 'POST')]
    public function removeVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $vacationRequestRepo->find($id);

        if (is_null($vacationRequest)) {
            throw new InvalidArgumentException('Provided id does not correspond to a VacationRequest');
        }

        if (! $vacationRequest->isRemovable()) {
            throw new NotRemovableException();
        }

        /** @var User $user */
        $user = $this->getUser();
        if ($vacationRequest->getUser()->getId() !== $user->getId()) {
            throw new InvalidPermissionsException();
        }

        $entityManager->remove($vacationRequest);
        $entityManager->flush();

        return $this->redirectToRoute('app_employee');
    }

    #[Route('/project-lead-grant/{id}', name: '_request_project_lead_grant', methods: 'POST')]
    public function grantProjectVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo, MessageBusInterface $bus): Response
    {
        $vacationRequest = $this->getVacationRequest($id, $vacationRequestRepo);
        $this->projectLeadApproval(true, $vacationRequest, $entityManager);
        $bus->dispatch(new ApprovalNotification($vacationRequest->getId()));

        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }

    #[Route('/project-lead-reject/{id}', name: '_request_project_lead_reject', methods: 'POST')]
    public function rejectProjectVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $this->getVacationRequest($id, $vacationRequestRepo);
        $this->projectLeadApproval(false, $vacationRequest, $entityManager);
        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }

    #[Route('/team-lead-grant/{id}', name: '_request_team_lead_grant', methods: 'POST')]
    public function grantTeamVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo, MessageBusInterface $bus): Response
    {
        $vacationRequest = $this->getVacationRequest($id, $vacationRequestRepo);
        $this->teamLeadApproval(true, $vacationRequest, $entityManager);
        $bus->dispatch(new ApprovalNotification($vacationRequest->getId()));

        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }

    #[Route('/team-lead-reject/{id}', name: '_request_team_lead_reject', methods: 'POST')]
    public function rejectTeamVacationRequest(int $id, EntityManagerInterface $entityManager, VacationRequestRepository $vacationRequestRepo): Response
    {
        $vacationRequest = $this->getVacationRequest($id, $vacationRequestRepo);
        $this->teamLeadApproval(false, $vacationRequest, $entityManager);
        return $this->redirectToRoute('app_employee_check_vacation_requests');
    }

    /**
     * @return VacationRequest
     * @throws InvalidArugmentException
     */
    protected function getVacationRequest(int $id, VacationRequestRepository $vacationRequestRepo): VacationRequest
    {
        $vacationRequest = $vacationRequestRepo->find($id);

        if (is_null($vacationRequest)) {
            throw new InvalidArgumentException('Provided id does not correspond to a VacationRequest');
        }

        return $vacationRequest;
    }

    /**
     * @return void
     * @throws InvalidPermssionsException
     */
    protected function teamLeadApproval(bool $isApproved, VacationRequest $vacationRequest, EntityManagerInterface $entityManager): void
    {
        $employee = $vacationRequest->getUser();
        $employeeTeams = $employee->getTeams()->toArray();

        /** @var User $user */
        $user = $this->getUser();
        $granteeTeams = $user->leaderOfTeams();

        $teamMatch = array_unique(array_merge($employeeTeams, $granteeTeams), SORT_REGULAR);

        if (count($teamMatch) === 0) {
            throw new InvalidPermissionsException();
        }

        $vacationRequest->setIsApprovedByTeamLead($isApproved);
        $vacationRequest->setApprovedByTeamLead($user);
        $entityManager->persist($vacationRequest);
        $entityManager->flush();
    }

    /**
     * @return void
     * @throws InvalidPermssionsException
     */
    protected function projectLeadApproval(bool $isApproved, VacationRequest $vacationRequest, EntityManagerInterface $entityManager): void
    {
        $employee = $vacationRequest->getUser();
        $employeeProjects = $employee->getProjects();

        /** @var User $user */
        $user = $this->getUser();
        $granteeProjects = $user->leaderOfProjects();

        $projectMatch = array_unique(array_merge($employeeProjects, $granteeProjects), SORT_REGULAR);

        if (count($projectMatch) === 0) {
            throw new InvalidPermissionsException();
        }

        $vacationRequest->setIsApprovedByProjectLead($isApproved);
        $vacationRequest->setApprovedByProjectLead($user);
        $entityManager->persist($vacationRequest);
        $entityManager->flush();
    }

}
