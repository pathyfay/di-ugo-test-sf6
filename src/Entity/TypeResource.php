<?php

namespace App\Entity;

use App\Repository\TypeResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeResourceRepository::class)]
class TypeResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_active = null;


    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'typeResource')]
    private Collection $resources;

    public function __construct()
    {
        $this->resources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function getResources(): Collection
    {
        return $this->resources;
    }

    public function setResources(Collection $resources): void
    {
        $this->resources = $resources;
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
//            'resources' => implode(',', array_map(fn(Resource $resource) => $resource->toArray(), $this->getResources()->toArray()))
        ];
    }

    public function __toString(): string
    {
        return (string)$this->getId();
    }
}
