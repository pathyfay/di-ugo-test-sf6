<?php

namespace App\Entity;

use App\Repository\OrganismeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganismeRepository::class)]
class Organisme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\OneToMany(targetEntity: Resource::class, mappedBy: 'organisme')]
    private Collection $resources;

    public function __construct()
    {
        $this->resources = new ArrayCollection();
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo = null): static
    {
        $this->logo = $logo;

        return $this;
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
            'reference' => $this->getReference(),
            'note' => $this->getNote(),
            'logo' => $this->getLogo(),
            'resources' => array_map(fn(Resource $resource) => $resource->getId(), $this->getResources()->toArray())
        ];
    }

    public function __toString(): string
    {
        return (string)$this->getId();
    }
}
