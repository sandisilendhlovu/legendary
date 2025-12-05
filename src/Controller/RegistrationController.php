<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use App\Service\RegistrationService;
use App\Service\VerificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class RegistrationController extends AbstractController
{
    public function __construct(
        private RegistrationService $registrationService,
        private VerificationService $verificationService,
        private MailerService $mailerService,
    ) {
    }

    #[Route('/register', name: 'auth_register', methods: ['GET', 'POST'])]
    public function register (Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Delegate registration logic to the service
            $token = $this->registrationService->registerUser($user, $plainPassword);


          // Generate a secure tokenized verification URL
            $verifyUrl = $this->generateUrl(
                'auth_verify_email',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

           // Email it to the user
            $this->mailerService->sendVerificationEmail($user, $verifyUrl);


          // Redirect user to verification notice page
            return $this->redirectToRoute('auth_verify_notice');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email/{token}', name: 'auth_verify_email', methods: ['GET'], requirements: ['token' => '[A-Za-z0-9]+'])]
    public function verifyUserEmail(string $token): Response
    {
        $verified = $this->verificationService->verifyEmail($token);


        if (!$verified) {
            $this->addFlash('error', 'Invalid or expired verification link.');
            return $this->redirectToRoute('home');
        }

        $this->addFlash('success', 'Your email address has been successfully verified.');

        return $this->redirectToRoute('auth_login');
     }
}
