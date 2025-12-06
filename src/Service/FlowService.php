<?php

namespace App\Service;

use App\Entity\Flow;
use App\Entity\FlowStep;
use App\Repository\FlowRepository;
use App\Repository\FlowStepRepository;

class FlowService
{
    public function __construct(
        private FlowRepository $flowRepository,
        private FlowStepRepository $flowStepRepository,
    ) {}

    /**
     * Returns a Flow object by ID or null if not found.
     */
    public function getFlow(int $flowId): ?Flow
    {
        return $this->flowRepository->find($flowId);
    }

    /**
     * Returns the step for a flow, or null if this is the end of the flow.
     */
    public function getFlowStep(Flow $flow, int $stepNumber): ?FlowStep
    {
        return $this->flowStepRepository->findOneBy([
            'flow' => $flow,
            'stepNumber' => $stepNumber,
        ]);
    }
}
