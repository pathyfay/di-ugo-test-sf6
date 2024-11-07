<?php

namespace App\DataTransformer;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UserInput;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\Uid\Uuid;

#[AsTaggedItem(index: 'input', priority: 10)]
final class UserInputDataTransformer implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof UserInput) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        /** @var User $user */
        $user = $context['object_to_populate'] ?? new User();
        if (!$user->getUuid()) {
            $user->setUuid(Uuid::v4()->toRfc4122());
        }
        $user->setFirstName($data->firstname);
        $user->setLastName($data->lastname);
        $user->setEmail($data->email);
        $user->setPhone($data->phone);
        $user->setCity($data->city);
        $user->setStreetAddress($data->streetAddress);
        $user->setPostalCode($data->postalCode);
        $user->setMatriculate($data->matriculate);
        $user->setDateOfBirth($data->dateOfBirth);
        $user->setCivility($data->civility);
        $user->setCivilStatus($data->civilStatus);
        $user->setRoles($data->roles ?? []);
        $user->setPhoto($data->photo);

        if (!empty($data->plainPassword)) {
            $hashedPassword = $this->hasher->hashPassword($user, $data->plainPassword);
            $user->setPassword($hashedPassword);
        }

        return $this->persistProcessor->process($user, $operation, $uriVariables, $context);
    }
}