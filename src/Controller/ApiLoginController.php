<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'app_api')]
class ApiLoginController extends AbstractController
{
    #[Route('/login_check', name: '_login_check')]
    public function loginCheck(): Response
    {
        throw new Exception('json_login in security.yaml should intercept this api call.');
    }
}
