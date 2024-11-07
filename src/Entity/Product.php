<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiResource\ProductApiController;
use App\Dto\ProductOutput;
use App\Repository\ProductRepository;
use DateTimeInterface;
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
            description: 'Get all products',
            normalizationContext: ['groups' => 'product_read'],
            output: ProductOutput::class,
            forceEager: false
        ),
        new Delete(),
        new Get(
            uriTemplate: '/products/{id}',
            uriVariables: ['id'],
            controller: ProductApiController::class . '::getProductById',
            description: 'Get product by id',
            normalizationContext: ['groups' => 'product_read'],
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
            uriTemplate: '/products-new',
            controller: ProductApiController::class . '::createProduct',
            description: 'Post a specific product',
            deserialize: false
        )
    ],
    normalizationContext: ['groups' => ['product_read'], 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['product_write']]
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_read', 'order_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: false)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private ?string $shortNom = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private ?string $reference = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private ?float $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private DateTimeInterface $updatedAt;

    #[ORM\Column(length: 255)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private ?string $currency;

    #[ORM\ManyToOne(targetEntity: CategoryProduct::class, inversedBy: 'products')]
    #[Groups(['product_read', 'product_write'])]
    private ?CategoryProduct $categories = null;

    #[ORM\OneToOne(targetEntity: Stock::class, mappedBy: 'product', cascade: ['persist', 'remove'])]
    #[Groups(['product_read'])]
    private ?Stock $stocks = null;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'products', fetch: 'LAZY')]
    #[Groups(['order_read', 'product_read', 'product_write'])]
    #[MaxDepth(1)]
    private Collection $orders;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user_read', 'order_read', 'product_read', 'product_write'])]
    private ?string $picture = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->currency = 'euros';
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
        return $this->shortNom;
    }

    public function setShortNom(?string $shortNom): void
    {
        $this->shortNom = $shortNom;
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

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
        }

        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getCategories(): ?CategoryProduct
    {
        return $this->categories;
    }

    public function setCategories(?CategoryProduct $categories): void
    {
        $this->categories = $categories;
    }

    public function getStocks(): ?Stock
    {
        return $this->stocks;
    }

    public function setStocks(?Stock $stocks): void
    {
        $this->stocks = $stocks;
    }

    public function getArray(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'short_nom' => $this->getShortNom(),
            'reference' => $this->getReference(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'currency' => $this->getCurrency(),
            'picture' => $this->getPicture(),
            'orders' => $this->orders->toArray(),
            'categories' => $this->categories->toArray(),
            'stocks' => $this->stocks->toArray()
        ];

    }

    public function __toString(): string
    {
        return (string)$this->getId();
    }
}
