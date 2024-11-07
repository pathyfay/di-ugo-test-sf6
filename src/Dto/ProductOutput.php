<?php

namespace App\Dto;


use App\Entity\Stock;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductOutput
{
    #[Groups(['product_read'])]
    public ?int $id = null;

    #[Groups(['product_read'])]
    public ?string $nom = null;

    #[Groups(['product_read'])]
    public ?string $shortNom = null;

    #[Groups(['product_read'])]
    public ?string $reference = null;

    #[Groups(['product_read'])]
    public ?string $description = null;

    #[Groups(['product_read'])]
    public ?float $price = null;

    #[Groups(['product_read'])]
    public ?string $currency = null;


    #[Groups(['product_read'])]
    public ?DateTimeImmutable $createdAt = null;

    #[Groups(['product_read'])]
    public ?DateTimeImmutable $updatedAt = null;

    #[Groups(['product_read'])]
    public ?string $picture = null;

    #[Groups(['product_read'])]
    public ?string $category = null;

    #[Groups(['product_read'])]
    public ?string $tags = null;

    #[Groups(['product_read'])]
    public ?Stock $stock = null;
}