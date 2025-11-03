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
}
