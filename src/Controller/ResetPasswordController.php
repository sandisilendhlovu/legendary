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
use Symfony\Component\HttpFoundation\RequestStack;


final class ResetPasswordController extends AbstractController

   {

    #[Route('/forgot-password/confirmation', name: 'app_forgot_password_confirmation')]
    public function confirmation(): Response
    {
        return $this->render('reset_password/forgot_password_confirmation.html.twig');
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager): Response 
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

    // DEV ONLY: Generate a temporary reset URL (to test flow)
    $resetUrl = $this->generateUrl('app_reset_password', [
        'selector' => $selector,
        'verifier' => $verifier,
    ], UrlGeneratorInterface::ABSOLUTE_URL);

    $this->addFlash('info', sprintf('DEV ONLY: Reset link → %s', $resetUrl));
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
      
// 1️⃣ Validate token
    $token = $entityManager->getRepository(PasswordResetToken::class)->findOneBy(['selector' => $selector]);

    if (!$token || $token->getExpiresAt() < new \DateTimeImmutable() || $token->getUsedAt() !== null) {
        $this->addFlash('danger', 'This password reset link is invalid or expired.');
        return $this->redirectToRoute('app_forgot_password');
    }

    if (!password_verify($verifier, $token->getVerifierHash())) {
        $this->addFlash('danger', 'Invalid password reset link.');
        return $this->redirectToRoute('app_forgot_password');
    }

    // 2️⃣ Build form
    $form = $this->createForm(\App\Form\ResetPasswordType::class);
    $form->handleRequest($request);

    // 3️⃣ Handle submission
    if ($form->isSubmitted() && $form->isValid()) {

    // Check if passwords match
    $newPassword = $form->get('plainPassword')->get('first')->getData();
    $confirmPassword = $form->get('plainPassword')->get('second')->getData();
 
    if ($newPassword !== $confirmPassword) {
    return $this->render('reset_password/reset_form.html.twig', [
        'resetForm' => $form->createView(),
        'error_message' => 'Passwords do not match.',
     ]);
     }

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

    // 4️⃣ Render the form
    return $this->render('reset_password/reset_form.html.twig', [
        'resetForm' => $form->createView(),
    ]);
}

   }