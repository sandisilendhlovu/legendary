<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\PasswordResetToken;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetRequestService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Handles the creation of a password reset request.
     *
     * If the user exists, creates a reset token and returns the
     * selector + verifier pair needed to build the reset URL.
     *
     * Always returns an array, but may contain nulls if user doesn't exist.
     */
    public function createResetRequest(string $email): array
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return [null, null, null];
        }

        // Generate secure random values
        $selector = bin2hex(random_bytes(9));      // short visible id
        $verifier = bin2hex(random_bytes(32));     // long secret
        $verifierHash = password_hash($verifier, PASSWORD_DEFAULT);

        // Create token entity
        $token = new PasswordResetToken();
        $token->setUser($user);
        $token->setSelector($selector);
        $token->setVerifierHash($verifierHash);
        $token->setCreatedAt(new \DateTimeImmutable());
        $token->setExpiresAt(new \DateTimeImmutable('+1 hour'));

        // Persist token
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return [$user, $selector, $verifier];
    }
}
