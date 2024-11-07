<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use App\ApiResource\OrderApiController;
use App\DataTransformer\OrderOutputDataTransformer;
use App\Dto\OrderOutput;
use App\Repository\OrderRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiFilter(SearchFilter::class, properties: [
    'user.id' => 'exact',
    'products.id' => 'exact',
    'currency' => 'exact',
    'price' => 'exact',
    'quantity' => 'exact',
    'createdAt' => 'exact',
    'status' => 'exact',
    'date' => 'exact',
])]
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 100,
            paginationMaximumItemsPerPage: 200,
            order: ['id' => 'DESC'],
            description: 'Get all orders',
            output: OrderOutput::class,
            forceEager: false,
            processor: OrderOutputDataTransformer::class
        ),
        new Get(
            uriTemplate: '/orders/{id}',
            uriVariables: ['id'],
            controller: OrderApiController::class . '::getOrderById',
            paginationEnabled: true,
            paginationItemsPerPage: 100,
            paginationMaximumItemsPerPage: 200,
            description: 'Get order by id',
            forceEager: false
        ),
        new Put(
            uriTemplate: '/orders/{id}/edit',
            uriVariables: ['id'],
            controller: OrderApiController::class . '::updateOrder',
            paginationEnabled: true,
            paginationItemsPerPage: 100,
            paginationMaximumItemsPerPage: 200,
            description: 'Put a specific order',
            deserialize: false
        ),
        new Post(
            uriTemplate: '/orders-create',
            controller: OrderApiController::class . '::createOrder',
            paginationEnabled: true,
            paginationItemsPerPage: 100,
            paginationMaximumItemsPerPage: 200,
            description: 'Post a specific order',
            deserialize: false
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['file_user_read']],
    denormalizationContext: ['groups' => ['file_user_write']]
)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user_read', 'order_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], fetch: "LAZY", inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['user_read', 'order_read'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['user_read', 'order_read'])]
    private int $quantity = 0;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'orders', cascade: ['persist'], fetch: "EAGER")]
    #[ORM\JoinTable(name: "order_product")]
    private Collection $products;

    #[ORM\Column]
    #[Groups(['user_read', 'order_read'])]
    private int $tva = 0;

    #[ORM\Column(length: 255)]
    #[Groups(['user_read', 'order_read'])]
    private ?string $currency = null;

    #[ORM\Column]
    #[Groups(['user_read', 'order_read'])]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user_read', 'order_read'])]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user_read', 'order_read'])]
    private DateTimeInterface $updatedAt;

    #[ORM\Column]
    #[Groups(['user_read', 'order_read'])]
    private int $totalAmount = 0;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
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
            $this->totalAmount += $product->getPrice() ?? 0;
            $this->quantity = $this->products->count() + 1;
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);
        $this->totalAmount -= $product->getPrice();
        $this->quantity = $this->products->count() - 1;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTva(): ?int
    {
        return $this->tva;
    }

    public function setTva(int $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getArray(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'quantity' => $this->getQuantity(),
            'products' => $this->getProducts()->toArray(),
            'tva' => $this->getTva(),
            'currency' => $this->getCurrency(),
            'totalAmount' => $this->getTotalAmount(),
        ];
    }

    public function __toString(): string
    {
        return (string)$this->getId();
    }

}
