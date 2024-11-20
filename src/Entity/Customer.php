<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\ApiResource\CustomerOrdersController;
use App\Repository\CustomerRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/customers',
            controller: CustomerOrdersController::class . '::getCustomers',
            description: 'Get all customers',
            normalizationContext: ['groups' => 'customer_details']),

        new GetCollection(
            uriTemplate: '/customers/{id}/orders',
            uriVariables: ['id'],
            controller: CustomerOrdersController::class . '::getCustomerOrders',
            description: 'Get orders for a specific customer',
            normalizationContext: ['groups' => 'order_details']
        ),

        new Put(
            uriTemplate: '/customers/{id}/edit',
            uriVariables: ['id'],
            controller: CustomerOrdersController::class . '::updateCustomerOrders',
            description: 'Put a specific customer',
            deserialize: false
        ),

        new Post(
            uriTemplate: '/customers/new',
            controller: CustomerOrdersController::class . '::createCustomerOrders',
            description: 'Post a specific customer',
            deserialize: false
        ),

        new Delete(
            uriTemplate: '/customers/{id}/delete',
            uriVariables: ['id'],
            controller: CustomerOrdersController::class . '::deleteCustomerOrders',
            description: 'Delete a specific customer',
        )
    ]
)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[Groups(['customer_details'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['customer_details'])]
    private string $title;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['customer_details'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['customer_details'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['customer_details'])]
    private ?string $postal_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['customer_details'])]
    private ?string $city = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['customer_details'])]
    private ?string $email = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'customer', cascade: ['persist', 'remove'])]
    #[Groups(['customer_details', 'order_details'])]
    private Collection $orders;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['customer_details'])]
    private ?string $mobile = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['customer_details'])]
    private ?DateTimeInterface $birthday = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['customer_details'])]
    private ?string $photo = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return $this
     */
    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return $this
     */
    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    /**
     * @param string|null $postal_code
     * @return $this
     */
    public function setPostalCode(?string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return $this
     */
    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCustomer($this);
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
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @param string|null $mobile
     * @return $this
     */
    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getBirthday(): ?DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param DateTimeInterface|null $birthday
     * @return $this
     */
    public function setBirthday(?DateTimeInterface $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     * @return $this
     */
    public function setPhoto(string|null $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->lastname;
    }
}
