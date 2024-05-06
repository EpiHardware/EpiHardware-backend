<?php

namespace App\Entity;

use AllowDynamicProperties;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


#[AllowDynamicProperties] #[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: "email_unique", columns: ["email"])]
class User implements UserInterface, PasswordAuthenticatedUserInterface{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\SequenceGenerator(sequenceName: 'entity_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $login = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $firstname = null;

    #[ORM\OneToOne(targetEntity: Cart::class, inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $lastname = null;
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $orders;

    public function getId(): ?int {
        return $this->id;
    }

    public function getLogin(): ?string {
        return $this->login;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function setLogin(string $login): self {
        $this->login = $login;
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self {
        $this->lastname = $lastname;
        return $this;
    }

    public function getRoles(): array {
        return ['ROLE_USER']; // Adjust accordingly based on your application's roles logic
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function getUserIdentifier(): string {
        return $this->email; // This typically returns a unique identifier, often the email
    }

    public function getUsername(): string {
        // Deprecated in Symfony 5.3+, use getUserIdentifier instead
        return $this->getUserIdentifier();
    }

    public function getSalt(): ?string {
        // Not needed when using the "bcrypt" algorithm in password_hash
        return null;
    }

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getCart(): ?Cart {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self {
        $this->cart = $cart;
        return $this;
    }
}
