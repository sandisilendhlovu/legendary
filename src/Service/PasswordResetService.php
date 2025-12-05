<?php

namespace App\Service;

use App\Entity\PasswordResetToken;
use App\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordResetService
{
    public function __construct(
        private PasswordResetTokenRepository $tokenRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Validates a reset token and returns it if valid.
     * Returns null if token is invalid, expired, or already used.
     */
    public function validateResetToken(string $selector, string $verifier): ?PasswordResetToken
    {
        $token = $this->tokenRepository->findOneBy(['selector' => $selector]);

        if (!$token) {
            return null;
        }

        // Token expired or already used
        if ($token->getExpiresAt() < new \DateTimeImmutable() ||
            $token->getUsedAt() !== null) {
            return null;
        }

        // Verify the verifier secret
        if (!password_verify($verifier, $token->getVerifierHash())) {
            return null;
        }

        return $token;
    }

    /**
     * Updates the user's password and marks the token as used.
     */
    public function resetPassword(PasswordResetToken $token, string $newPassword): void
    {
        $user = $token->getUser();

        // Hash new password using Symfony's UserPasswordHasherInterface
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        // Mark the token as used
        $token->setUsedAt(new \DateTimeImmutable());

        $this->entityManager->flush();
    }
}
