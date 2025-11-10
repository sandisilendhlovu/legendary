<?php

namespace App\Entity;

use App\Repository\FlowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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

// Each Flow must belong to one Product
#[ORM\ManyToOne(inversedBy: 'flows')]
#[ORM\JoinColumn(nullable: false, options: ['comment' => 'The product this flow belongs to'])]
private ?Product $product = null;


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
}
