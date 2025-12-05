<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * Handles all business logic for registering a new user.
     *
     * - Hashes the password
     * - Sets default fields
     * - Generates a verification token
     * - Persists the user
     *
     * Returns the generated verification token.
     */
    public function registerUser(User $user, string $plainPassword): string
    {
        // Hash and set password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Set default fields
        $now = new \DateTimeImmutable();
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);
        $user->setIsActive(true);
        $user->setIsVerified(false);

        // Generate and set verification token
        $token = bin2hex(random_bytes(32));
        $user->setVerificationToken($token);

        // Persist user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $token;
    }
}
