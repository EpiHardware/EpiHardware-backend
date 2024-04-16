<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class ShoppingCart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: CartItem::class, mappedBy: 'cart', cascade: ['persist', 'remove'])]
    private Collection $items;

    public function __construct() {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getItems(): Collection {
        return $this->items;
    }

    public function addItem(CartItem $item): self {
        $this->items[] = $item;
        return $this;
    }

    public function removeItem(CartItem $item): self {
        $this->items->removeElement($item);
        return $this;
    }
}

