<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class RelationChild
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['relation_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserRelation::class, inversedBy: 'relationChildren')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['relation_read', 'relation_write'])]
    private ?UserRelation $relation = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['relation_read', 'relation_write'])]
    private ?User $child = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'relation_child_parents')]
    #[Groups(['relation_read', 'relation_write'])]
    private Collection $parents;

    public function __construct()
    {
        $this->parents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getRelation(): ?UserRelation
    {
        return $this->relation;
    }

    public function setRelation(?UserRelation $relation): void
    {
        $this->relation = $relation;
    }

    public function getChild(): ?User
    {
        return $this->child;
    }

    public function setChild(?User $child): void
    {
        $this->child = $child;
    }

    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function setParents(Collection $parents): void
    {
        $this->parents = $parents;
    }

    public function addParent(User $user): void
    {
        if (!$this->parents->contains($user)) {
            $this->parents->add($user);
        }
    }

    public function removeParent(User $user): void
    {
        $this->parents->removeElement($user);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'relationId' => $this->relation?->getId(),
            'child' => $this->child ? [
                'id' => $this->child->getId(),
                'firstName' => $this->child->getFirstName(),
                'lastName' => $this->child->getLastName(),
            ] : null,
            'parents' => array_map(
                fn(User $p) => [
                    'id' => $p->getId(),
                    'firstName' => $p->getFirstName(),
                    'lastName' => $p->getLastName(),
                ],
                $this->parents->toArray()
            ),
        ];
    }
}