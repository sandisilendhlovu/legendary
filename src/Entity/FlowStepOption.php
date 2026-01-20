<?php

namespace App\Entity;

use App\Repository\FlowStepOptionRepository;
use Doctrine\ORM\Mapping as ORM;


 #[ORM\Entity(repositoryClass: FlowStepOptionRepository::class)]
 #[ORM\HasLifecycleCallbacks]

 class FlowStepOption

{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Label shown on the button (e.g. "Yes", "No", "Retry", "Run Line Test")
    #[ORM\Column(
        length: 150,
        nullable: false,
        options: ['comment' => 'Button label that represents this choice in the flow']
    )]
    private ?string $label = null;

    // The step where this option appears
    #[ORM\ManyToOne(inversedBy: 'flowStepOptions')]
    #[ORM\JoinColumn(nullable: false, options: ['comment' => 'The FlowStep this option belongs to'])]
    private ?FlowStep $currentStep = null;

    // The step this option leads to (nullable because some actions end the flow)
    #[ORM\ManyToOne(targetEntity: FlowStep::class)]
    #[ORM\JoinColumn(nullable: true, options: ['comment' => 'The next step triggered when this option is selected'])]
    private ?FlowStep $nextStep = null;

     #[ORM\Column(type: 'datetime_immutable')]
     private ?\DateTimeImmutable $createdAt = null;

     #[ORM\Column(type: 'datetime_immutable', nullable: true)]
     private ?\DateTimeImmutable $updatedAt = null;

     // === Lifecycle Callbacks ===
     #[ORM\PrePersist]
     public function onPrePersist(): void
     {
         $now = new \DateTimeImmutable();
         $this->createdAt = $now;
         $this->updatedAt = $now;
     }

     #[ORM\PreUpdate]
     public function onPreUpdate(): void
     {
         $this->updatedAt = new \DateTimeImmutable();
     }

     // === Getters & Setters ===

     public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getCurrentStep(): ?FlowStep
    {
        return $this->currentStep;
    }

    public function setCurrentStep(?FlowStep $currentStep): static
    {
        $this->currentStep = $currentStep;

        return $this;
    }

    public function getNextStep(): ?FlowStep
    {
        return $this->nextStep;
    }

    public function setNextStep(?FlowStep $nextStep): static
    {
        $this->nextStep = $nextStep;

        return $this;
    }

     public function getCreatedAt(): ?\DateTimeImmutable
     {
         return $this->createdAt;
     }

     public function getUpdatedAt(): ?\DateTimeImmutable
     {
         return $this->updatedAt;
     }
}
