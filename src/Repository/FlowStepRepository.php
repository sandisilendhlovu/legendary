<?php

namespace App\Repository;

use App\Entity\Flow;
use App\Entity\FlowStep;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FlowStep>
 */
class FlowStepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlowStep::class);
    }

    public function findStep(Flow $flow, int $stepNumber): ?FlowStep
    {
        return $this->findOneBy([
            'flow' => $flow,
            'stepNumber' => $stepNumber,
        ]);
    }
}
