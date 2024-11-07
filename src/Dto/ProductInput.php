<?php

namespace App\Dto;


use App\Entity\Stock;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductInput
{
    #[Groups(['product_write'])]
    public ?string $nom = null;

    #[Groups(['product_write'])]
    public ?string $shortNom = null;

    #[Groups(['product_write'])]
    public ?string $reference = null;

    #[Groups(['product_write'])]
    public ?string $description = null;

    #[Groups(['product_write'])]
    public ?float $price = null;

    #[Groups(['product_write'])]
    public ?string $currency = null;


    #[Groups(['product_write'])]
    public ?DateTimeImmutable $createdAt = null;

    #[Groups(['product_write'])]
    public ?DateTimeImmutable $updatedAt = null;

    #[Groups(['product_write'])]
    public ?string $picture = null;

    #[Groups(['product_write'])]
    public ?string $category = null;

    #[Groups(['product_write'])]
    public ?string $tags = null;

    #[Groups(['product_write'])]
    public ?Stock $stock = null;
}