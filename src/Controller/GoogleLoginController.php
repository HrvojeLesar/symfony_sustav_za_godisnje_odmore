<?php

namespace App\Controller;

use App\Service\GoogleOAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/google', name: 'app_google')]
class GoogleLoginController extends AbstractController
{
    #[Route('/login', name: '_login')]
    public function login(GoogleOAuthService $googleOAuthService): Response
    {
        $uri = $googleOAuthService->getLoginUrl();
        return $this->redirect($googleOAuthService->getLoginUrl());
    }

    #[Route('/callback', name: '_callback')]
    public function callback(GoogleOAuthService $googleOAuthService): Response
    {
        $googleOAuthService->handleCallback();
        throw new \Exception('Unimplemented');
    }

    #[Route('/finish', name: '_finish')]
    public function finish(GoogleOAuthService $googleOAuthService): Response
    {
        throw new \Exception('Unimplemented');
    }
}
