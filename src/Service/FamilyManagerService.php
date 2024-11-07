<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRelationRepository;
use App\Repository\RelationChildRepository;

class FamilyManagerService
{
    private UserRelationRepository $userRelationRepository;
    private RelationChildRepository $relationChildRepository;

    public function __construct(
        UserRelationRepository $userRelationRepository,
        RelationChildRepository $relationChildRepository
    ) {
        $this->userRelationRepository = $userRelationRepository;
        $this->relationChildRepository = $relationChildRepository;
    }

    /**
     * 🔹 Récupère toutes les relations (couples, unions, etc.) d’un utilisateur.
     */
    public function getAllRelations(User $user): array
    {
        return $this->userRelationRepository->findActiveRelationsForUser($user->getId());
    }

    /**
     * 🔹 Récupère tous les enfants d’un utilisateur (dans toutes ses relations).
     */
    public function getAllChildren(User $user): array
    {
        $relationChildren = $this->relationChildRepository->findChildrenByParent($user->getId());
        return array_map(fn($rc) => $rc->getChild(), $relationChildren);
    }

    /**
     * 🔹 Récupère les parents d’un enfant donné.
     */
    public function getAllParents(User $child): array
    {
        $relationChildren = $this->relationChildRepository->findBy(['child' => $child]);
        $parents = [];

        foreach ($relationChildren as $rc) {
            foreach ($rc->getParents() as $p) {
                $parents[$p->getId()] = $p; // éviter les doublons
            }
        }

        return array_values($parents);
    }

    /**
     * 🔹 Construit une vue complète de la “famille” d’un utilisateur.
     * Combine relations + enfants + parents dans un tableau structuré.
     */
    public function getFullFamilyTree(User $user): array
    {
        $relations = $this->getAllRelations($user);
        $children = $this->getAllChildren($user);
        $parents = $this->getAllParents($user);

        return [
            'user' => [
                'id' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ],
            'relations' => array_map(fn($r) => $r->toArray(), $relations),
            'children' => array_map(fn($c) => [
                'id' => $c->getId(),
                'firstName' => $c->getFirstName(),
                'lastName' => $c->getLastName(),
            ], $children),
            'parents' => array_map(fn($p) => [
                'id' => $p->getId(),
                'firstName' => $p->getFirstName(),
                'lastName' => $p->getLastName(),
            ], $parents),
        ];
    }
}
