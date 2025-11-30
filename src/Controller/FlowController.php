<?php

namespace App\Controller;

use App\Entity\Flow;
use App\Entity\FlowStep;
use App\Repository\FlowRepository;
use App\Repository\FlowStepRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FlowController extends AbstractController
{
    #[Route('/flow/{flow_id}/{step}', name: 'flow_view', methods: ['GET'], requirements: ['flow_id' => '\d+', 'step' => '\d+'], defaults: ['step' => 1])]
    public function view(
        int $flow_id,
        int $step,
        FlowRepository $flowRepository,
        FlowStepRepository $flowStepRepository
    ): Response {
        $flow = $flowRepository->find($flow_id);

        if (!$flow) {
            throw $this->createNotFoundException('Flow not found.');
        }

        // Find the current step
        $currentStep = $flowStepRepository->findOneBy([
            'flow' => $flow,
            'stepNumber' => $step
        ]);

        if (!$currentStep) {
            // End of flow
            return $this->render('flow/complete.html.twig', [
                'flow' => $flow,
            ]);
        }

        return $this->render('flow/view.html.twig', [
            'flow' => $flow,
            'step' => $currentStep,
            'options' => $currentStep->getFlowStepOptions(),
        ]);
    }
}

