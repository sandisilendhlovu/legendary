<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig)
    { }

    /**
     * Send password-reset link to user
     */
    public function sendPasswordResetEmail($user, string $resetUrl): void
    {
        $html = $this->twig->render('emails/password_reset.html.twig', [
            'user'     => $user,
            'resetUrl' => $resetUrl,
        ]);

        $email = (new Email())
            ->from('noreply@sandycodes.co.za')
            ->to($user->getEmail())
            ->subject('Legendary | Password Reset Request')
            ->html($html);

        $this->mailer->send($email);
    }

    /**
     * Send account-verification email after registration
     */
    public function sendVerificationEmail($user, string $verifyUrl): void
    {
        $html = $this->twig->render('emails/verify_notice.html.twig', [
            'user'      => $user,
            'verifyUrl' => $verifyUrl,
        ]);

        $email = (new Email())
            ->from('Legendary <noreply@sandycodes.co.za>')
            ->to($user->getEmail())
            ->subject('Legendary | Verify Your Email Address')
            ->html($html);

        $this->mailer->send($email);
    }
}
