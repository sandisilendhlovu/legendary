<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TroubleshootingController extends AbstractController
{
    #[Route('/troubleshooting', name: 'troubleshooting', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('troubleshooting/index.html.twig');
    }

    #[Route('/troubleshooting/airmobile', name: 'troubleshooting_airmobile', methods: ['GET'])]
    public function airmobile(): Response
    {
        return $this->render('troubleshooting/airmobile.html.twig');
    }

    #[Route('/troubleshooting/wireless', name: 'troubleshooting_wireless', methods: ['GET'])]
    public function wireless(): Response
    {
    return $this->render('troubleshooting/wireless.html.twig');
    }

    #[Route('/troubleshooting/dsl', name: 'troubleshooting_dsl', methods: ['GET'])]
     public function dsl(): Response
    {
    return $this->render('troubleshooting/dsl.html.twig');
    }

    #[Route('/troubleshooting/fibre-orders', name: 'troubleshooting_fibre_orders', methods: ['GET'])]
    public function fibreOrders(): Response
    {
    return $this->render('troubleshooting/fibre_orders.html.twig');
    }

    #[Route('/troubleshooting/fibre-technical', name: 'troubleshooting_fibre_technical', methods: ['GET'])]
    public function fibreTechnical(): Response
   {
    return $this->render('troubleshooting/fibre_technical.html.twig');
   }

   #[Route('/troubleshooting/voip', name: 'troubleshooting_voip', methods: ['GET'])]
    public function voip(): Response
   {
    return $this->render('troubleshooting/voip.html.twig');
   }

   #[Route('/troubleshooting/hosting', name: 'troubleshooting_hosting', methods: ['GET'])]
   public function hosting(): Response
   {
    return $this->render('troubleshooting/hosting.html.twig');
   }

   #[Route('/troubleshooting/accounts', name: 'troubleshooting_accounts', methods: ['GET'])]
   public function accounts(): Response
  {
    return $this->render('troubleshooting/accounts.html.twig');
  }


}
