<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['stock_read']],
    denormalizationContext: ['groups' => ['stock_write']]
)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stock_read', 'product_read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'stock', targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stock_read', 'stock_write'])]
    private ?Product $product = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['stock_read', 'stock_write', 'product_read'])]
    private int $quantity = 0;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['stock_read', 'stock_write'])]
    private ?string $location = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['stock_read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product' => $this->product?->getId(),
            'quantity' => $this->quantity,
            'location' => $this->location,
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s')
        ];
    }
}
