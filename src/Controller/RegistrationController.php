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

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));


            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $user->setIsActive(true);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

 

            // generate a signed url and email it to the user
          $verifyUrl = $this->generateUrl('app_verify_email', [
         'email' => $user->getEmail(),
         'token' => bin2hex(random_bytes(16)),
         ], UrlGeneratorInterface::ABSOLUTE_URL);

          $this->mailerService->sendVerificationEmail($user, $verifyUrl);


            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_verify_notice');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
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

          // Look up user by email
          $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

          if (!$user) {
          $this->addFlash('error', 'No user found for this email.');
          return $this->redirectToRoute('app_home');
    }

         // Mark as verified
         $user->setIsVerified(true);
         $this->entityManager->flush();

         $this->addFlash('success', 'Your email address has been successfully verified.');

        return $this->redirectToRoute('app_login');

    }
    }
