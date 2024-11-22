<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiResource\ProductApiController;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/products',
            controller: ProductApiController::class . '::getProducts',
            description: 'Get all products',
            normalizationContext: ['groups' => 'order_details'],
            forceEager: false
        ),
        new Delete(),
        new Get(
            uriTemplate: '/products/{id}',
            uriVariables: ['id'],
            controller: ProductApiController::class . '::getProductById',
            description: 'Get product by id',
            normalizationContext: ['groups' => 'order_details'],
            forceEager: false
        ),

        new Put(
            uriTemplate: '/products/{id}/edit',
            uriVariables: ['id'],
            controller: ProductApiController::class . '::updateProduct',
            description: 'Put a specific product',
            deserialize: false
        ),

        new Post(
            uriTemplate: '/products/new',
            controller: ProductApiController::class . '::createProduct',
            description: 'Post a specific product',
            deserialize: false
        )
    ],
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['customer_details', 'order_details'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: false)]
    #[Groups(['customer_details', 'order_details'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: false)]
    #[Groups(['customer_details', 'order_details'])]
    private ?string $short_nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['customer_details', 'order_details'])]
    private ?string $reference = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['customer_details', 'order_details'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['customer_details', 'order_details'])]
    private ?float $price = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'product', fetch: 'LAZY')]
    #[Groups(['order_details'])]
    #[MaxDepth(1)]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getShortNom(): ?string
    {
        return $this->short_nom;
    }

    public function setShortNom(?string $short_nom): void
    {
        $this->short_nom = $short_nom;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getProduct() === $this) {
                $order->setProduct(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }

}
