<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

final class FilterToFileOutput
{
    #[Groups(['filter_to_file_read'])]
    public ?int $id = null;

    #[Groups(['filter_to_file_read'])]
    public ?string $name = null;

    #[Groups(['filter_to_file_read'])]
    public ?string $regex = null;

    #[Groups(['filter_to_file_read'])]
    public ?string $status = null;

    #[Groups(['filter_to_file_read'])]
    public ?\DateTime $dateCreated = null;

    #[Groups(['filter_to_file_read'])]
    public int $priority = 0;
}