<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ForgotPasswordRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;



final class ResetPasswordController extends AbstractController
{
    #[Route('/reset/password', name: 'app_reset_password')]
    public function index(): Response

    {
        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
        ]);
    } 

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager): Response 

    {
    $form = $this->createForm(ForgotPasswordRequestType::class);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $email = $form->get('email')->getData();

        $this->addFlash('success', sprintf(
            'If an account exists for %s, a password reset link will be sent shortly.',
            $email
        ));

        // Redirect to a temporary confirmation page
        return $this->redirectToRoute('app_forgot_password_confirmation');
    }

    return $this->render('reset_password/forgot_password.html.twig', [
        'requestForm' => $form->createView(),
    ]);
}

#[Route('/forgot-password/confirmation', name: 'app_forgot_password_confirmation')]
public function confirmation(): Response
{
    return $this->render('reset_password/forgot_password_confirmation.html.twig');
}

}
