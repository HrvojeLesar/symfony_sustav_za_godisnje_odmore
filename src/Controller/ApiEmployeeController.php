<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiEmployeeController extends AbstractController
{
    #[Route('/api/employee', name: 'app_api_employee')]
    public function index(TokenInterface $token): Response
    {
        /** @var User $user */
        $user = $token->getUser();

        return $this->json($user);
    }
}
