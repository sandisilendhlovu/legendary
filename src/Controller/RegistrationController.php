<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\MailerService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class RegistrationController extends AbstractController
{
    public function __construct(
    private EntityManagerInterface $entityManager,
    private MailerService $mailerService
    )

    {
    }

    #[Route('/register', name: 'auth_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hash the user's plain password before saving to the database
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));


            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $user->setIsActive(true);
            $user->setIsVerified(false);

            $token = bin2hex(random_bytes(32));
            $user->setVerificationToken($token);



            $this->entityManager->persist($user);
            $this->entityManager->flush(); // user gets saved with token



          // Generate a secure tokenized verification URL and email it to the user
            $verifyUrl = $this->generateUrl(
                'auth_verify_email',
                ['token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL
            );


            $this->mailerService->sendVerificationEmail($user, $verifyUrl);


           // Email verification link sent — redirect user to verification notice

            return $this->redirectToRoute('app_verify_notice');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email/{token}', name: 'auth_verify_email', methods: ['GET'], requirements: ['token' => '[A-Za-z0-9]+'])]
    public function verifyUserEmail(string $token): Response
    {
        // Find the user by the verification token
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Invalid or expired verification link.');
            return $this->redirectToRoute('app_home');
        }

        // Mark the user's account as verified and clear the token
        $user->setIsVerified(true);
        $user->setVerificationToken(null);

        $this->entityManager->flush();

        $this->addFlash('success', 'Your email address has been successfully verified.');

        return $this->redirectToRoute('app_login');
    }
    }
