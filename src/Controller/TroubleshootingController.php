<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TroubleshootingController extends AbstractController
{
    #[Route('/troubleshooting', name: 'app_troubleshooting')]
    public function index(): Response
    {
        return $this->render('troubleshooting/index.html.twig');
    }

    #[Route('/troubleshooting/airmobile', name: 'app_troubleshooting_airmobile')]
    public function airmobile(): Response
    {
        return $this->render('troubleshooting/airmobile.html.twig');
    }

    #[Route('/troubleshooting/wireless', name: 'app_troubleshooting_wireless')]
    public function wireless(): Response
    {
    return $this->render('troubleshooting/wireless.html.twig');
    }

    #[Route('/troubleshooting/dsl', name: 'app_troubleshooting_dsl')]
     public function dsl(): Response
    {
    return $this->render('troubleshooting/dsl.html.twig');
    }
}
