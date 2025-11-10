<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(
    length: 100,
    nullable: false,
    options: ['comment' => 'Product name, e.g. Fibre, DSL, VoIP, Mobile, Hosting']
  )]
  private ?string $name = null;

   #[ORM\Column(
    type: Types::TEXT,
    nullable: true,
    options: ['comment' => 'Description of the product']
  )]
   private ?string $description = null;

   /**
    * @var Collection<int, Flow>
    */
   #[ORM\OneToMany(targetEntity: Flow::class, mappedBy: 'product')]
   private Collection $flows;

   public function __construct()
   {
       $this->flows = new ArrayCollection();
   }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Flow>
     */
    public function getFlows(): Collection
    {
        return $this->flows;
    }

    public function addFlow(Flow $flow): static
    {
        if (!$this->flows->contains($flow)) {
            $this->flows->add($flow);
            $flow->setProduct($this);
        }

        return $this;
    }

    public function removeFlow(Flow $flow): static
    {
        if ($this->flows->removeElement($flow)) {
            // set the owning side to null (unless already changed)
            if ($flow->getProduct() === $this) {
                $flow->setProduct(null);
            }
        }

        return $this;
    }
}
