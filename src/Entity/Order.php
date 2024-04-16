<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?float $totalPrice = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['persist', 'remove'])]
    private Collection $products;

    public function __construct() {
        $this->products = new ArrayCollection();
    }

    // Getters and setters

    public function getId(): ?int {
        return $this->id;
    }

    public function getTotalPrice(): ?float {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self {
        $this->creationDate = $creationDate;
        return $this;
    }

    public function getProducts(): Collection {
        return $this->products;
    }

    public function addProduct(OrderItem $product): self {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setOrder($this);
        }
        return $this;
    }

    public function removeProduct(OrderItem $product): self {
        if ($this->products->removeElement($product)) {
            if ($product->getOrder() === $this) {
                $product->setOrder(null);
            }
        }
        return $this;
    }
}

