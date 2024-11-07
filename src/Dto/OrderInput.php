<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderInput
{
    #[Groups(['order_write'])]
    public $status = null;

    #[Groups(['order_write'])]
    private $quantity = null;

    #[Groups(['order_write'])]
    private $currency = null;

    #[Groups(['order_write'])]
    private $price = null;

    #[Groups(['order_write'])]
    private $createdAt = null;

    #[Groups(['order_write'])]
    private $updatedAt = null;

    #[Assert\NotBlank(groups: ['order_write'])]
    #[Groups(['order_write'])]
    public ?float $totalAmount = null;

    #[Assert\NotBlank(groups: ['order_write'])]
    #[Groups(['order_write'])]
    public ?int $userId = null;
}