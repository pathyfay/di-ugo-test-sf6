<?php

namespace App\Handler\Message;

use App\Message\MailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class MailMessageHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $defaultFrom = 'patrician.tankwey@gmail.com'
    ) {}

    public function __invoke(MailMessage $msg): void
    {
        $email = (new Email())
            ->from(new Address($msg->from ?? $this->defaultFrom, 'FTK TECH'))
            ->to($msg->to)
            ->subject($msg->subject);

        $msg->isHtml ? $email->html($msg->body) : $email->text($msg->body);

        $this->mailer->send($email);

        $this->logger->info('[MailWorker] Mail envoyé ✅', [
            'to' => $msg->to,
            'subject' => $msg->subject,
        ]);
    }
}