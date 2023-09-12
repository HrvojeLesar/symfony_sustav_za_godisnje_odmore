<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/api', name: 'app_api')]
class ApiLoginController extends AbstractController
{
    #[Route('/login', name: '_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('api_login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/login_check', name: '_login_check')]
    public function loginCheck(): Response
    {
        throw new Exception('form_login in security.yaml should intercept this api call.');
    }

    #[Route('/login_check_form', name: '_login_check_form')]
    public function loginCheckForm(): Response
    {
        throw new Exception('form_login in security.yaml should intercept this api call.');
    }
}
