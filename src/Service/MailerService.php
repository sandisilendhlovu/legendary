<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService extends AbstractController
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send password-reset link to user
     */
    public function sendPasswordResetEmail($user, string $resetUrl): void
    {
        $email = (new Email())
            ->from('noreply@sandycodes.co.za')
            ->to($user->getEmail())
            ->subject('Legendary | Password Reset Request')
            ->html($this->renderView('emails/password_reset.html.twig', [
                'user'     => $user,
                'resetUrl' => $resetUrl,
            ]));

        $this->mailer->send($email);
    }

    /**
     * Send account-verification email after registration
     */
    public function sendVerificationEmail($user, string $verifyUrl): void
    {
        $email = (new Email())
            ->from('noreply@sandycodes.co.za')
            ->to($user->getEmail())
            ->subject('Legendary | Verify Your Email Address')
            ->html($this->renderView('emails/verify_notice.html.twig', [
                'user'      => $user,
                'verifyUrl' => $verifyUrl,
            ]));

        $this->mailer->send($email);
    }
}
