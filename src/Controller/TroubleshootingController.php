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

    #[Route('/troubleshooting/fibre-orders', name: 'app_troubleshooting_fibre_orders')]
    public function fibreOrders(): Response
    {
    return $this->render('troubleshooting/fibre_orders.html.twig');
    }

    #[Route('/troubleshooting/fibre-technical', name: 'app_troubleshooting_fibre_technical')]
    public function fibreTechnical(): Response
   {
    return $this->render('troubleshooting/fibre_technical.html.twig');
   }

   #[Route('/troubleshooting/voip', name: 'app_troubleshooting_voip')]
    public function voip(): Response
   {
    return $this->render('troubleshooting/voip.html.twig');
   }

   #[Route('/troubleshooting/hosting', name: 'app_troubleshooting_hosting')]
   public function hosting(): Response
   {
    return $this->render('troubleshooting/hosting.html.twig');
   }

   #[Route('/troubleshooting/accounts', name: 'app_troubleshooting_accounts')]
   public function accounts(): Response
  {
    return $this->render('troubleshooting/accounts.html.twig');
  }


}
