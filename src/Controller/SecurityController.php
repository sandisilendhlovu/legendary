<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'auth_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // Retrieve the last entered username and any login error
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
             'error' => $error,
            ]);
    }

    #[Route(path: '/logout', name: 'auth_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Symfony handles logout automatically based on the firewall configuration
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

   #[Route('/verify-notice', name: 'auth_verify_notice',methods: ['GET'])]
    public function verifyNotice(): Response
    {
        return $this->render('security/verify_notice.html.twig');
    }
}
