<?php

namespace App\Controller;

use App\Entity\AnnualVacation;
use App\Entity\VacationRequest;
use App\Entity\User;
use App\Form\VacationRequestFormType;
use App\Repository\AnnualVacationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

            $annualVacation = $user->getAnnualVacations()
                ->filter(function (AnnualVacation $av) {
                    return $av->getYear() === date('Y');
                })
                ->first();

            if (is_null($annualVacation)) {
                throw new Exception("Annual vacation not found");
            }

            $vacationRequest->setUser($user);
            $vacationRequest->setAnnualVacation($annualVacation);
            $entityManager->persist($vacationRequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_employee');
        }

        $errors = $form->getErrors();

        return $this->render('vacation_request/index.html.twig', [
            'controller_name' => 'VacationRequestController',
            'form' => $form,
        ]);
    }
}
