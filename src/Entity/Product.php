<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: 'entity_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(type: 'integer')]
    #[Groups(['order:items'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['order:items'])]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['order:items'])]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['order:items'])]
    private ?string $photo = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['order:items'])]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'products')]
    private ?Order $order = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }
}
