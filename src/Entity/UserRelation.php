<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class UserRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['relation_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'relationsPrimary')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['relation_read', 'relation_write'])]
    private ?User $user1 = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'relationsSecondary')]
    #[Groups(['relation_read', 'relation_write'])]
    private ?User $user2 = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'relationsTertiary')]
    #[Groups(['relation_read', 'relation_write'])]
    private ?User $user3 = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'relationsQuaternary')]
    #[Groups(['relation_read', 'relation_write'])]
    private ?User $user4 = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'user_relation_others')]
    #[Groups(['relation_read', 'relation_write'])]
    private Collection $others;

    #[ORM\ManyToOne(targetEntity: CivilStatus::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['relation_read', 'relation_write'])]
    private ?CivilStatus $civilStatus = null;

    #[ORM\OneToMany(targetEntity: RelationChild::class, mappedBy: 'relation', cascade: ['persist', 'remove'])]
    #[Groups(['relation_read', 'relation_write'])]
    private Collection $relationChildren;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $weddingDate = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $divorceDate = null;

    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable();
        $this->others = new ArrayCollection();
        $this->relationChildren = new ArrayCollection();
    }

    // --- Getters / Setters ---

    public function getId(): ?int { return $this->id; }

    public function getUser1(): ?User { return $this->user1; }
    public function setUser1(?User $user1): void { $this->user1 = $user1; }

    public function getUser2(): ?User { return $this->user2; }
    public function setUser2(?User $user2): void { $this->user2 = $user2; }

    public function getUser3(): ?User { return $this->user3; }
    public function setUser3(?User $user3): void { $this->user3 = $user3; }

    public function getUser4(): ?User { return $this->user4; }
    public function setUser4(?User $user4): void { $this->user4 = $user4; }

    public function getOthers(): Collection { return $this->others; }
    public function addOther(User $user): void
    {
        if (!$this->others->contains($user)) {
            $this->others->add($user);
        }
    }
    public function removeOther(User $user): void
    {
        $this->others->removeElement($user);
    }

    public function getCivilStatus(): ?CivilStatus
    {
        return $this->civilStatus;
    }

    public function setCivilStatus(?CivilStatus $civilStatus): void
    {
        $this->civilStatus = $civilStatus;
    }

    public function getRelationChildren(): Collection { return $this->relationChildren; }

    public function addRelationChild(RelationChild $relationChild): void
    {
        if (!$this->relationChildren->contains($relationChild)) {
            $this->relationChildren->add($relationChild);
            $relationChild->setRelation($this);
        }
    }

    public function removeRelationChild(RelationChild $relationChild): void
    {
        if ($this->relationChildren->removeElement($relationChild)) {
            if ($relationChild->getRelation() === $this) {
                $relationChild->setRelation(null);
            }
        }
    }

    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void { $this->updatedAt = $updatedAt; }

    public function getWeddingDate(): ?DateTimeImmutable { return $this->weddingDate; }
    public function setWeddingDate(?DateTimeImmutable $weddingDate): void { $this->weddingDate = $weddingDate; }

    public function getDivorceDate(): ?DateTimeImmutable { return $this->divorceDate; }
    public function setDivorceDate(?DateTimeImmutable $divorceDate): void { $this->divorceDate = $divorceDate; }

    // --- 🔥 toArray helper ---

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'civilStatus' => $this->civilStatus?->value,
            'user1' => $this->user1?->getId(),
            'user2' => $this->user2?->getId(),
            'user3' => $this->user3?->getId(),
            'user4' => $this->user4?->getId(),
            'others' => array_map(fn(User $u) => $u->getId(), $this->others->toArray()),
            'children' => array_map(
                fn(RelationChild $rc) => [
                    'child' => $rc->getChild()?->getId(),
                    'parents' => array_map(fn(User $p) => $p->getId(), $rc->getParents()->toArray())
                ],
                $this->relationChildren->toArray()
            ),
            'weddingDate' => $this->weddingDate?->format('Y-m-d'),
            'divorceDate' => $this->divorceDate?->format('Y-m-d'),
            'updatedAt' => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}