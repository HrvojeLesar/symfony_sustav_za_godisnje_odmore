<?php

namespace App\Controller;

use App\Entity\User;
use App\Exceptions\GoogleAuthFailedException;
use App\Form\GoogleRegistrationFormType;
use App\Form\RegisterFormType;
use App\Service\GoogleOAuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/register', name: 'app_registration')]
class RegistrationController extends AbstractController
{
    #[Route('', name: '')]
    public function index(UserPasswordHasherInterface $passwordHasher, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/register-google', name: '_google')]
    public function registerGoogle(Request $request, EntityManagerInterface $entityManager, GoogleOAuthService $googleOAuthService): Response
    {
        try {
            $email = $googleOAuthService->getEmail();
        } catch (GoogleAuthFailedException) {
            $this->redirectToRoute('app_registration');
        }

        $user = new User();
        $form = $this->createForm(GoogleRegistrationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEmail($email);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/index-google.html.twig', [
            'form' => $form,
        ]);
    }
}
