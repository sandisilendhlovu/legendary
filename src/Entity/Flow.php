<?php

namespace App\Entity;

use App\Repository\FlowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: FlowRepository::class)]
class Flow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

// Title of the troubleshooting flow (e.g. "Fibre: LOS Light Red")
 #[ORM\Column(
                            length: 150,
                            nullable: false,
                            options: ['comment' => 'Short title for this troubleshooting flow']
                        )]
                        private ?string $title = null;

 // Description or summary of the flow
 #[ORM\Column(
                            type: Types::TEXT,
                            nullable: true,
                            options: ['comment' => 'Optional description or summary of the troubleshooting flow']
                        )]
                        private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;


// Each Flow must belong to one Product
#[ORM\ManyToOne(inversedBy: 'flows')]
#[ORM\JoinColumn(nullable: false, options: ['comment' => 'The product this flow belongs to'])]
private ?Product $product = null;

/**
 * @var Collection<int, FlowStep>
 */
#[ORM\OneToMany(targetEntity: FlowStep::class, mappedBy: 'flow')]
private Collection $flowSteps;

public function __construct()
{
    $this->flowSteps = new ArrayCollection();
}

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



    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, FlowStep>
     */
    public function getFlowSteps(): Collection
    {
        return $this->flowSteps;
    }

    public function addFlowStep(FlowStep $flowStep): static
    {
        if (!$this->flowSteps->contains($flowStep)) {
            $this->flowSteps->add($flowStep);
            $flowStep->setFlow($this);
        }

        return $this;
    }

    public function removeFlowStep(FlowStep $flowStep): static
    {
        if ($this->flowSteps->removeElement($flowStep)) {
            // set the owning side to null (unless already changed)
            if ($flowStep->getFlow() === $this) {
                $flowStep->setFlow(null);
            }
        }

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
