<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TroubleshootingController extends AbstractController
{
    #[Route('/troubleshooting', name: 'troubleshooting_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('troubleshooting/index.html.twig');
    }

    #[Route('/troubleshooting/{product}', name: 'troubleshooting_product', methods: ['GET'])]
    public function product(string $product): Response
    {
        // Allowed troubleshooting products — whitelist for safety (prevents loading invalid templates)
        $allowedProducts = [
            'airmobile',
            'wireless',
            'dsl',
            'fibre-orders',
            'fibre-technical',
            'voip',
            'hosting',
            'accounts',
        ];

        // Validate the product slug (URL-friendly name used in the route - must match one of the allowed categories)
        if (!in_array($product, $allowedProducts, true)) {
            throw $this->createNotFoundException('Troubleshooting category not found.');
        }

        // Convert slug to matching template name (hyphens become underscores)
        $templateName = str_replace('-', '_', $product);

    return $this->render(sprintf('troubleshooting/%s.html.twig', $templateName));
  }


}
