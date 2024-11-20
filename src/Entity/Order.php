<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['customer_details', 'order_details'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['customer_details', 'order_details'])]
    private DateTimeInterface $order_date;

    #[ORM\ManyToOne(targetEntity: Customer::class, cascade: ['persist'], inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['customer_details', 'order_details'])]
    private ?Customer $customer = null;

    #[ORM\Column]
    #[Groups(['customer_details', 'order_details'])]
    private int $quantity = 0;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'], inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['customer_details', 'order_details'])]
    private ?Product $product =  null;

    #[ORM\Column]
    #[Groups(['customer_details', 'order_details'])]
    private int $price = 0;

    #[ORM\Column(length: 255)]
    #[Groups(['customer_details', 'order_details'])]
    private ?string $currency = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['customer_details', 'order_details'])]
    private DateTimeInterface $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

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

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getOrderDate(): DateTimeInterface
    {
        return $this->order_date;
    }

    public function setOrderDate(DateTime $order_date): void
    {
        $this->order_date = $order_date;
    }
}
