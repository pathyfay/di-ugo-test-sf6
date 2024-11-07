<?php

namespace App\Dto;

use DateTime;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class MessageOutput
{
    #[Groups(['message_read'])]
    public ?string $id = null;

    #[Groups(['message_read'])]
    public ?DateTime $created_at = null;

    #[Groups(['message_read'])]
    public ?string $attachment = null;

    #[Groups(['message_read'])]
    public ?string $object = null;

    #[Groups(['message_read'])]
    public ?string $msg = null;

    #[Groups(['message_read'])]
    public ?string $status = null;

    #[Groups(['message_read'])]
    public ?string $sender = null;

    #[Groups(['message_read'])]
    public ?string $receiver = null;
}