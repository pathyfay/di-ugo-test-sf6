<?php

namespace App\Dto;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

final class MessageInput {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?string $id = null;

    #[ORM\Column]
    public ?DateTime $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $attachment = null;

    #[ORM\Column(length: 255)]
    public ?string $object = null;

    #[ORM\Column(length: 255)]
    public ?string $msg = null;

    #[ORM\Column(length: 8)]
    public ?string $status = null;

    #[ORM\Column(length: 50)]
    public ?string $sender = null;

    #[ORM\Column(length: 255)]
    public ?string $receiver = null;
}