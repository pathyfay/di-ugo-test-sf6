<?php

// src/MessageHandler/MailMessageHandler.php
namespace App\MessageHandler;

use App\Message\MailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class MailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $defaultFrom = 'patrician.fayette@gmail.com'
    ) {}

    public function __invoke(MailMessage $msg): void
    {
        $email = (new Email())
            ->from(new Address($msg->from ?? $this->defaultFrom, 'FTK TECH'))
            ->to($msg->to)
            ->subject($msg->subject);

        $msg->isHtml ? $email->html($msg->htmlBody) : $email->text($msg->htmlBody);

        $this->mailer->send($email);
        $this->logger->info('[MailWorker] Mail envoyé', ['to' => $msg->to, 'subject' => $msg->subject]);
    }
}
