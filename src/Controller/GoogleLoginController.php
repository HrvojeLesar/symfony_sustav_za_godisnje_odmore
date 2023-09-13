<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Security\LoginAuthenticator;
use App\Service\GoogleOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/google', name: 'app_google')]
class GoogleLoginController extends AbstractController
{
    #[Route('/login', name: '_login', methods: 'GET')]
    public function login(GoogleOAuthService $googleOAuthService): Response
    {
        $uri = $googleOAuthService->getLoginUrl();
        return $this->redirect($googleOAuthService->getLoginUrl());
    }

    #[Route('/callback', name: '_callback', methods: 'GET')]
    public function callback(GoogleOAuthService $googleOAuthService): Response
    {
        $googleOAuthService->handleCallback();
        return $this->redirectToRoute('app_google_finish');
    }

    #[Route('/finish', name: '_finish')]
    public function finish(Security $security, GoogleOAuthService $googleOAuthService, UserRepository $userRepository): Response
    {
        $email = $googleOAuthService->getEmail();
        $user = $userRepository->getUserByEmail($email);
        if (is_null($user)) {
            return $this->redirectToRoute('app_registration_google');
        }
        $redirectResponse = $security->login($user, LoginAuthenticator::class);
        return $redirectResponse;
    }
}
