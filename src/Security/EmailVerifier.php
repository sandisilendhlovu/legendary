<?php

namespace App\Security;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Uid\Uuid;

class EmailVerifier
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function sendEmailConfirmation(string $verifyRouteName, $user): void
    {
        // Generate a unique token
        $token = Uuid::v4()->toRfc4122();

        // Generate the verification URL
        $verificationUrl = $this->urlGenerator->generate($verifyRouteName, [
            'token' => $token,
            'email' => $user->getEmail(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // Build the email
        $email = (new Email())
            ->from(new Address('noreply@sandycodes.co.za', 'Legendary Login System'))
            ->to($user->getEmail())
            ->subject('Please verify your email address')
            ->html("<p>Hi {$user->getFirstName()},</p>
                    <p>Thank you for registering your profile. Please verify your email by clicking the link below:</p>
                    <p><a href='{$verificationUrl}'>Verify Email</a></p>");

        // Send the email
        $this->mailer->send($email);
    }
}
