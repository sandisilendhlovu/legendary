<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class VerificationService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Verifies a user by a given verification token.
     *
     * Returns true on success, false if token is invalid.
     */
    public function verifyEmail(string $token): bool
    {
        $user = $this->userRepository->findOneBy([
            'verificationToken' => $token,
        ]);

        if (!$user) {
            return false;
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $user->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return true;
    }
}
