<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RessourceRepository::class)]
class Ressource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[ORM\ManyToOne(inversedBy: 'ressources')]
    private TypeRessource $type;

    #[ORM\Column]
    private ?float $montant_total = null;

    #[ORM\Column]
    private ?float $montant_restant = null;

    #[ORM\Column(nullable: true)]
    private ?float $mensualite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $taux = null;

    #[ORM\Column]
    private ?float $reserve = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_prelevement = null;

    #[ORM\Column(length: 255)]
    #[ORM\ManyToOne(inversedBy: 'ressources')]
    private Operation $operation;

    #[ORM\Column(length: 255)]
    private ?string $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'ressources')]
    private ?Organisme $organisme = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): TypeRessource
    {
        return $this->type;
    }

    public function setType(TypeRessource $type): static
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getDatePrelevement(): ?\DateTimeInterface
    {
        return $this->date_prelevement;
    }

    public function setDatePrelevement(\DateTimeInterface $date_prelevement): static
    {
        $this->date_prelevement = $date_prelevement;

        return $this;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function setOperation(Operation $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    public function getUtilisateur(): ?string
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(string $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getOrganisme(): ?Organisme
    {
        return $this->organisme;
    }

    public function setOrganisme(?Organisme $organisme): static
    {
        $this->organisme = $organisme;

        return $this;
    }
}
