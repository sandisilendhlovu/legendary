<?php

namespace App\Entity;

use App\Repository\FlowStepRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FlowStepRepository::class)]
class FlowStep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

 // Step number in the flow sequence (e.g. 1, 2, 3...)
 #[ORM\Column(
                            type: 'integer',
                            nullable: false,
                            options: ['comment' => 'Order number of this step within its flow']
                         )]
                         private ?int $stepNumber = null;

 // Short title for the step
 #[ORM\Column(
                            length: 150,
                            nullable: false,
                            options: ['comment' => 'Short title describing this troubleshooting step']
                         )]
                         private ?string $title = null;

 // Detailed content or instructions for the step
 #[ORM\Column(
                            type: Types::TEXT,
                            nullable: false,
                            options: ['comment' => 'Detailed instructions or information for this troubleshooting step']
                         )]
                         private ?string $content = null;

 // Each step belongs to one Flow
 #[ORM\ManyToOne(inversedBy: 'flowSteps')]
                         #[ORM\JoinColumn(nullable: false, options: ['comment' => 'The flow this step belongs to'])]
                         private ?Flow $flow = null;

    /**
     * @var Collection<int, FlowStepOption>
     */
    #[ORM\OneToMany(targetEntity: FlowStepOption::class, mappedBy: 'currentStep')]
    private Collection $flowStepOptions;

    public function __construct()
    {
        $this->flowStepOptions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStepNumber(): ?int
    {
        return $this->stepNumber;
    }

    public function setStepNumber(int $stepNumber): static
    {
        $this->stepNumber = $stepNumber;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getFlow(): ?Flow
    {
        return $this->flow;
    }

    public function setFlow(?Flow $flow): static
    {
        $this->flow = $flow;

        return $this;
    }

    /**
     * @return Collection<int, FlowStepOption>
     */
    public function getFlowStepOptions(): Collection
    {
        return $this->flowStepOptions;
    }

    public function addFlowStepOption(FlowStepOption $flowStepOption): static
    {
        if (!$this->flowStepOptions->contains($flowStepOption)) {
            $this->flowStepOptions->add($flowStepOption);
            $flowStepOption->setCurrentStep($this);
        }

        return $this;
    }

    public function removeFlowStepOption(FlowStepOption $flowStepOption): static
    {
        if ($this->flowStepOptions->removeElement($flowStepOption)) {
            // set the owning side to null (unless already changed)
            if ($flowStepOption->getCurrentStep() === $this) {
                $flowStepOption->setCurrentStep(null);
            }
        }

        return $this;
    }
}
