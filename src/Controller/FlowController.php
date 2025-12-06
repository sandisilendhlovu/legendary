<?php

namespace App\Controller;

use App\Service\FlowService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FlowController extends AbstractController

{
    public function __construct(
        private FlowService $flowService,
    ) {}

    #[Route('/flow/{flow_id}/{step}', name: 'flow_view', methods: ['GET'], requirements: ['flow_id' => '\d+', 'step' => '\d+'], defaults: ['step' => 1])]
    public function view(int $flow_id, int $step): Response
    {
      // Get the flow
        $flow = $this->flowService->getFlow($flow_id);

        if (!$flow) {
            throw $this->createNotFoundException('Flow not found.');
        }

       // Get the current step
       $currentStep = $this->flowService->getFlowStep($flow, $step);


        // If no step exists → flow is complete
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

