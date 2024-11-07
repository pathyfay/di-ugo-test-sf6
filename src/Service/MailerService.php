<?php

namespace App\Service;

use App\Message\MailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
        private string $defaultFrom = 'patrician.fayette@gmail.com',
        private bool $mailerEnabled = true,
        private bool $asyncDefault = false
    ) {}

    /**
     * 🔹 Envoi intelligent : async ou direct
     */
    public function send(
        string $to,
        string $subject,
        string $body,
        bool $isHtml = true,
        ?string $from = null,
        ?bool $async = null
    ): bool {
        if (!$this->mailerEnabled) {
            $this->logger->info('[MailerService] Envoi désactivé.', compact('to', 'subject'));
            return false;
        }

        // logique async
        $useAsync = $async ?? $this->asyncDefault;
        if ($useAsync) {
            $this->bus->dispatch(new MailMessage($to, $subject, $body, $from, $isHtml));
            $this->logger->info('[MailerService] 📬 Mail dispatché en asynchrone.', compact('to', 'subject'));
            return true;
        }

        // sinon : envoi direct
        try {
            $email = (new Email())
                ->from(new Address($from ?? $this->defaultFrom, 'FTK TECH'))
                ->to($to)
                ->subject($subject);

            $isHtml ? $email->html($body) : $email->text($body);
            $this->mailer->send($email);

            $this->logger->info('[MailerService] ✉️ Mail envoyé directement.', compact('to', 'subject'));
            return true;
        } catch (Throwable $e) {
            $this->logger->error('[MailerService] Erreur envoi direct.', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Version raccourcie pour envoi asynchrone explicite
     */
    public function sendAsync(string $to, string $subject, string $html, ?string $from = null): void
    {
        $this->send($to, $subject, $html, true, $from, true);
    }

    /**
     * Version raccourcie pour envoi immédiat
     */
    public function sendNow(string $to, string $subject, string $html, ?string $from = null): bool
    {
        return $this->send($to, $subject, $html, true, $from, false);
    }
}