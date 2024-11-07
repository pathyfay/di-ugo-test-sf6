<?php

namespace App\Dto;

use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderOutput
{
    #[Groups(['order_read'])]
    public int $id;

    #[Groups(['order_read'])]
    public DateTime $createdAt;

    #[Groups(['order_read'])]
    public ?DateTime $updatedAt = null;

    #[Assert\NotBlank(groups: ['order_read'])]
    #[Groups(['order_read'])]
    public ?string $status = null;

    #[Assert\NotBlank(groups: ['order_read'])]
    #[Groups(['order_read'])]
    public ?float $totalAmount = null;

    #[Assert\NotBlank(groups: ['order_read'])]
    #[Groups(['order_read'])]
    public ?int $userId = null;
}