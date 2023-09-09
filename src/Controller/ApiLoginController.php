<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('api_login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/api/login_check', name: 'app_api_login_check')]
    public function loginCheck(): Response
    {
        throw new Exception('form_login in security.yaml should intercept this api call.');
    }

    #[Route('/api/login_check_form', name: 'app_api_login_check_form')]
    public function loginCheckForm(): Response
    {
        throw new Exception('form_login in security.yaml should intercept this api call.');
    }
}
