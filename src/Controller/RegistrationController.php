<?php

namespace App\Controller; 

use App\Entity\User; 
use App\Form\RegistrationFormType; 
use App\Security\EmailVerifier; 
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
    private EmailVerifier $emailVerifier,
    private EntityManagerInterface $entityManager,
    private MailerService $mailerService
    )

    {
    }

    #[Route('/register', name: 'app_register')]
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

            $this->entityManager->persist($user);
            $this->entityManager->flush();

 

          // Generate a secure tokenized verification URL and email it to the user
          $verifyUrl = $this->generateUrl('app_verify_email', [
         'email' => $user->getEmail(),
         'token' => bin2hex(random_bytes(16)),
         ], UrlGeneratorInterface::ABSOLUTE_URL);

          $this->mailerService->sendVerificationEmail($user, $verifyUrl);


           // Email verification link sent — redirect user to verification notice

            return $this->redirectToRoute('app_verify_notice');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        
            $email = $request->query->get('email');
            $token = $request->query->get('token');

            if (!$email || !$token) {
            $this->addFlash('error', 'Invalid verification link.');
            return $this->redirectToRoute('app_home');
    }

          // Retrieve the user associated with the provided email address
          $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

          if (!$user) {
          $this->addFlash('error', 'No user found for this email.');
          return $this->redirectToRoute('app_home');
    }

         // Mark the user's account as verified and save the changes to the database
         $user->setIsVerified(true);
         $this->entityManager->flush();

         $this->addFlash('success', 'Your email address has been successfully verified.');

        return $this->redirectToRoute('app_login');

    }
    }
