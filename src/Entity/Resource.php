<?php

namespace App\Entity;

use App\Repository\ResourceRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: TypeResource::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(name: 'type_resource_id', referencedColumnName: 'id', nullable: true)]
    private TypeResource $type;

    #[ORM\Column(nullable: true)]
    private ?float $montant_total = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant_restant = null;

    #[ORM\Column(nullable: true)]
    private ?float $mensualite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    private ?string $taux = null;

    #[ORM\Column(nullable: true)]
    private ?float $reserve = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $date_fin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $date_prelevement = null;

    #[ORM\ManyToOne(targetEntity: Operation::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(name: 'operation_id', referencedColumnName: 'id', nullable: true)]
    private ?Operation $operation = null;

    #[ORM\ManyToOne(targetEntity: Organisme::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(name: 'organisme_id', referencedColumnName: 'id', nullable: true)]
    private ?Organisme $organisme = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'resources')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getType(): TypeResource
    {
        return $this->type;
    }

    public function setType(?TypeResource $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montant_total;
    }

    public function setMontantTotal(float $montant_total): static
    {
        $this->montant_total = $montant_total;

        return $this;
    }

    public function getMontantRestant(): ?float
    {
        return $this->montant_restant;
    }

    public function setMontantRestant(float $montant_restant): static
    {
        $this->montant_restant = $montant_restant;

        return $this;
    }

    public function getMensualite(): ?float
    {
        return $this->mensualite;
    }

    public function setMensualite(?float $mensualite): static
    {
        $this->mensualite = $mensualite;

        return $this;
    }

    public function getTaux(): ?string
    {
        return $this->taux;
    }

    public function setTaux(string $taux): static
    {
        $this->taux = $taux;

        return $this;
    }

    public function getReserve(): ?float
    {
        return $this->reserve;
    }

    public function setReserve(float $reserve): static
    {
        $this->reserve = $reserve;

        return $this;
    }

    public function getDateDebut(): ?DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getDatePrelevement(): ?DateTimeInterface
    {
        return $this->date_prelevement;
    }

    public function setDatePrelevement(?DateTimeInterface $date_prelevement): void
    {
        $this->date_prelevement = $date_prelevement;
    }

    public function getOperation(): ?Operation
    {
        return $this->operation;
    }

    public function setOperation(?Operation $operation): void
    {
        $this->operation = $operation;
    }

    public function getResources(): Resource
    {
        return $this->resources;
    }

    public function setResources(Resource $resources): void
    {
        $this->resources = $resources;
    }

    public function getOrganisme(): ?Organisme
    {
        return $this->organisme;
    }

    public function setOrganisme(?Organisme $organisme): void
    {
        $this->organisme = $organisme;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUsers(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUsers(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getArray(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType()?->toArray(),
            'montant_total' => $this->getMontantTotal(),
            'montant_restant' => $this->getMontantRestant(),
            'mensualite' => $this->getMensualite(),
            'taux' => $this->getTaux(),
            'reserve' => $this->getReserve(),
            'date_debut' => $this->getDateDebut()?->format('Y-m-d H:i:s'),
            'date_fin' => $this->getDateFin()?->format('Y-m-d H:i:s'),
            'date_prelevement' => $this->getDatePrelevement()?->format('Y-m-d H:i:s'),
            'operation' => $this->getOperation()->getNom(),
            'organisme' => $this->getOrganisme()->getNom()
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}
