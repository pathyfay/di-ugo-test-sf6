<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    normalizationContext: ['groups' => ['category_read']],
    denormalizationContext: ['groups' => ['category_write']]
)]
class CategoryProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category_read', 'product_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category_read', 'category_write', 'product_read'])]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['category_read', 'category_write'])]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
    #[Groups(['category_read'])]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function setProducts(Collection $products): void
    {
        $this->products = $products;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'description' => $this->getDescription(),
            'products' =>array_map(fn(Product $product) => $product->toArray(), $this->getProducts()->toArray())
        ];
    }
}
