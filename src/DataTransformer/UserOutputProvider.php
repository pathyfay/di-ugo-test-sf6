<?php

namespace App\DataTransformer;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserOutput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserOutputProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|UserOutput|null
    {
        if ($operation instanceof Get) {
            $user = $this->em->getRepository(User::class)->find($uriVariables ?? null);
            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }
            return $this->toDto($user);
        }

        if ($operation instanceof GetCollection) {
            $users = $this->em->getRepository(User::class)->findAll();
            return array_map([$this, 'toDto'], $users);
        }

        return null;
    }

    private function toDto(User $user): UserOutput
    {
        $dto = new UserOutput();
        $dto->id = $user->getId();
        $dto->uuid = $user->getUuid() ?? '';
        $dto->firstname = $user->getFirstname();
        $dto->lastname = $user->getLastname();
        $dto->email = $user->getEmail() ?? '';
        $dto->phone = $user->getPhone();
        $dto->city = $user->getCity();
        $dto->postalCode = $user->getPostalCode();
        $dto->streetAddress = $user->getStreetAddress();
        $dto->matriculate = $user->getMatriculate();
        $dto->dateOfBirth = $user->getDateOfBirth();
        $dto->civility = $user->getCivility();
        $dto->civilStatus = $user->getCivilStatus();
        $dto->photo = $user->getPhoto();
        $dto->roles = $user->getRoles();

        return $dto;
    }
}