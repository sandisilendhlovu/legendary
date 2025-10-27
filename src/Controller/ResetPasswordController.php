<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordResetToken;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ForgotPasswordRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\MailerService; 




final class ResetPasswordController extends AbstractController
   {

    #[Route('/forgot-password/confirmation', name: 'app_forgot_password_confirmation')]
    public function confirmation(): Response
     {
        return $this->render('reset_password/forgot_password_confirmation.html.twig');
     }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, MailerService $mailerService): Response
      {


    $form = $this->createForm(ForgotPasswordRequestType::class);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $email = $form->get('email')->getData();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    if ($user) {
    // Generate secure random strings
    $selector = bin2hex(random_bytes(9));     // short visible id
    $verifier = bin2hex(random_bytes(32));    // long secret
    $verifierHash = password_hash($verifier, PASSWORD_DEFAULT);

    // Create and store token
    $token = new PasswordResetToken();
    $token->setUser($user);
    $token->setSelector($selector);
    $token->setVerifierHash($verifierHash);
    $token->setCreatedAt(new \DateTimeImmutable());
    $token->setExpiresAt(new \DateTimeImmutable('+1 hour'));

    $entityManager->persist($token);
    $entityManager->flush();

    // Build password reset URL
$resetUrl = $this->generateUrl('app_reset_password', [
    'selector' => $selector,
    'verifier' => $verifier,
], UrlGeneratorInterface::ABSOLUTE_URL);

$mailerService->sendPasswordResetEmail($user, $resetUrl);


 }

// Always show success message regardless of whether email exists
$this->addFlash('success', sprintf(
    'If an account exists for %s, a password reset link will be sent shortly.',
    $email
));

return $this->redirectToRoute('app_forgot_password_confirmation'); 

 }

   return $this->render('reset_password/forgot_password.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset/password/{selector}/{verifier}', name: 'app_reset_password')]
    public function resetPassword(Request $request, EntityManagerInterface $entityManager, string $selector, string $verifier): Response
    {
      
// Validate token
    $token = $entityManager->getRepository(PasswordResetToken::class)->findOneBy(['selector' => $selector]);

    if (!$token || $token->getExpiresAt() < new \DateTimeImmutable() || $token->getUsedAt() !== null) {
        $this->addFlash('danger', 'Your password reset link is invalid or has expired.');
        return $this->redirectToRoute('app_forgot_password');
    }

    if (!password_verify($verifier, $token->getVerifierHash())) {
        $this->addFlash('danger', 'Invalid password reset link.');
        return $this->redirectToRoute('app_forgot_password');
    }

    // Build form
    $form = $this->createForm(\App\Form\ResetPasswordType::class);
    $form->handleRequest($request);

    //Check for expired or invalid CSRF token and refresh the form safely
     if ($form->isSubmitted() && !$form->isValid()) {
      foreach ($form->getErrors(true) as $error) {
        if (str_contains($error->getMessage(), 'Invalid CSRF token')) {
            $this->addFlash('warning', 'Your session expired. Please try again.');
            return $this->redirectToRoute('app_reset_password', [
                'selector' => $selector,
                'verifier' => $verifier,
            ]);
        }
     }
  }

    
    // Handle submission
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData(); // returns the new password string

            if (strlen($newPassword) < 8) {
                $this->addFlash('danger', 'Your password must be at least 8 characters long.');
            } else {
                // Update user password
                $user = $token->getUser();
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $user->setPassword($hashedPassword);

                // Mark token as used
                $token->setUsedAt(new \DateTimeImmutable());
                $entityManager->flush();

                $this->addFlash('success', 'Your password has been successfully updated.');
                return $this->redirectToRoute('app_login');
            }
        }

        // Always render the form
        return $this->render('reset_password/reset_form.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}