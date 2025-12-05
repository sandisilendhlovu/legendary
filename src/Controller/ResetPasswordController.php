<?php

namespace App\Controller;

use App\Form\ForgotPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Service\PasswordResetRequestService;
use App\Service\PasswordResetService;
use App\Service\MailerService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


final class ResetPasswordController extends AbstractController
   {
        public function __construct(
            private PasswordResetRequestService $resetRequestService,
            private PasswordResetService $resetService,
            private MailerService $mailerService,
        ){}


    #[Route('/forgot-password/confirmation', name: 'auth_forgot_password_confirmation', methods: ['GET'])]
    public function confirmation(): Response
     {
        return $this->render('reset_password/forgot_password_confirmation.html.twig');
     }

    #[Route('/forgot-password', name: 'auth_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request): Response
    {


    $form = $this->createForm(ForgotPasswordRequestType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $email = $form->get('email')->getData();

        // Delegate creation of reset request
        [$user, $selector, $verifier] = $this->resetRequestService->createResetRequest($email);

        // If a user exists, send reset email
        if ($user && $selector && $verifier) {
            $resetUrl = $this->generateUrl(
                'auth_reset_password',
                ['selector' => $selector, 'verifier' => $verifier],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $this->mailerService->sendPasswordResetEmail($user, $resetUrl);
 }

// Always show success message regardless of whether email exists
$this->addFlash('success', sprintf(
    'If an account exists for %s, a password reset link will be sent shortly.',
    $email
));

    return $this->redirectToRoute('auth_forgot_password_confirmation');


 }

   return $this->render('reset_password/forgot_password.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset/password/{selector}/{verifier}', name: 'auth_reset_password', methods: ['GET', 'POST'], requirements: ['selector' => '[A-Za-z0-9]+', 'verifier' => '[A-Za-z0-9]+'])]
    public function resetPassword(Request $request, string $selector, string $verifier): Response
    {

// Validate token

        $token = $this->resetService->validateResetToken($selector, $verifier);

    if (!$token) {
        $this->addFlash('danger', 'Your password reset link is invalid or has expired.');
        return $this->redirectToRoute('auth_forgot_password');
    }

        // Build form
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

    // Handle valid form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();

            $this->resetService->resetPassword($token, $newPassword);

            $this->addFlash('success', 'Your password has been successfully updated.');
                return $this->redirectToRoute('auth_login');
            }

        // Always render the form
        return $this->render('reset_password/reset_form.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
