<?php

namespace App\Dto;

use App\Entity\Civility;
use App\Entity\CivilStatus;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserInput
{
    #[Groups(['user_write'])]
    public ?string $firstname = null;

    #[Groups(['user_write'])]
    public ?string $lastname = null;

    #[Groups(['user_write'])]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    #[Groups(['user_write'])]
    public ?string $matriculate = null;

    #[Groups(['user_write'])]
    public ?string $phone = null;

    #[Groups(['user_write'])]
    public ?string $mobile = null;

    #[Groups(['user_write'])]
    public ?string $city = null;

    #[Groups(['user_write'])]
    public ?string $postalCode = null;

    #[Groups(['user_write'])]
    public ?string $streetAddress = null;

    #[Groups(['user_write'])]
    public ?DateTime $dateOfBirth = null;

    #[Groups(['user_write'])]
    public ?Civility $civility = null;

    #[Groups(['user_write'])]
    public ?CivilStatus $civilStatus = null;

    #[Groups(['user_write'])]
    public array $roles = ['ROLE_USER'];

    #[Groups(['user_write'])]
    public ?bool $photo = null;

    #[Groups(['user_write'])]
    public array $assignedFiles = [];

    #[Groups(['user_write'])]
    public array $modifiedFiles = [];

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public ?string $plainPassword = null;
}