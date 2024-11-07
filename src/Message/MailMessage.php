<?php

namespace App\Message;

class MailMessage
{
    public function __construct(
        public readonly string $to,
        public readonly string $subject,
        public readonly string $htmlBody,
        public readonly ?string $from = null,
        public readonly bool $isHtml = true
    ) {}
}
