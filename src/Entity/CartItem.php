<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = 1;

    #[ORM\ManyToOne(targetEntity: ShoppingCart::class, inversedBy: 'items')]
    private ?ShoppingCart $cart = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getProduct(): ?Product {
        return $this->product;
    }

    public function setProduct(?Product $product): self {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): ?int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self {
        $this->quantity = $quantity;
        return $this;
    }

    public function getCart(): ?ShoppingCart {
        return $this->cart;
    }

    public function setCart(?ShoppingCart $cart): self {
        $this->cart = $cart;
        return $this;
    }
}
