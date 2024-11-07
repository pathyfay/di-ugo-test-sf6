<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class FilterToFileInput
{
    #[Assert\NotBlank]
    public ?string $name = null;

    public ?string $regex = null;

    public ?string $status = null;

    #[Assert\Type('int')]
    public ?int $priority = 0;

    public ?\DateTimeInterface $dateCreated = null;
}
