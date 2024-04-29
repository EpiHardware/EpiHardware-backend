<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ORM\Table(name: "carts")]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: 'entity_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: "cart")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?UserInterface $user = null;

    #[ORM\ManyToMany(targetEntity: Product::class)]
    #[ORM\JoinTable(name: "cart_products",
        joinColumns: [new ORM\JoinColumn(name: "cart_id", referencedColumnName: "id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "product_id", referencedColumnName: "id")])]
    private Collection $products;

    private array $quantities = [];

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->quantities = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            // Optionally, manage quantities here
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // Optionally, manage quantities here
        }
        return $this;
    }

    public function getQuantities(): array
    {
        return $this->quantities;
    }

    public function setQuantities(array $quantities): self
    {
        $this->quantities = $quantities;
        return $this;
    }

    public function getQuantityForProduct(Product $product): int
    {
        return $this->quantities[$product->getId()] ?? 0;
    }

    public function setQuantityForProduct(Product $product, int $quantity): self
    {
        $this->quantities[$product->getId()] = $quantity;
        return $this;
    }
}
