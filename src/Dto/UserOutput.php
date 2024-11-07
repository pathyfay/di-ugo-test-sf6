<?php

namespace App\Dto;

use App\Entity\Civility;
use App\Entity\CivilStatus;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

final class UserOutput
{
    #[Groups(['user_read'])]
    public int $id;

    #[Groups(['user_read'])]
    public string $uuid;

    #[Groups(['user_read'])]
    public ?string $firstname = null;

    #[Groups(['user_read'])]
    public ?string $lastname = null;

    #[Groups(['user_read'])]
    public string $email;

    #[Groups(['user_read'])]
    public ?string $phone = null;

    #[Groups(['user_read'])]
    public ?string $city = null;

    #[Groups(['user_read'])]
    public ?string $postalCode = null;

    #[Groups(['user_read'])]
    public ?string $streetAddress = null;

    #[Groups(['user_read'])]
    public ?string $matriculate = null;

    #[Groups(['user_read'])]
    public ?DateTime $dateOfBirth = null;

    #[Groups(['user_read'])]
    public Civility $civility;

    #[Groups(['user_read'])]
    public CivilStatus $civilStatus;

    #[Groups(['user_read'])]
    public ?string $photo = null;

    #[Groups(['user_read'])]
    public array $roles;
}
