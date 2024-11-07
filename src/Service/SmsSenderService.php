<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsSenderService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface     $logger,
        private string              $providerUrl,
        private string              $apiKey,
        private string              $senderName = 'FTK-IT',
        private bool                $enabled = true
    ){
    }

    /**
     * Envoie un SMS à un numéro donné
     */
    public function send(string $phoneNumber, string $message): bool
    {
        if (!$this->enabled) {
            $this->logger->info("[SmsSender] Envoi désactivé - message ignoré.",
                [
                    'to' => $phoneNumber,
                    'message' => $message,
                ]
            );
            return false;
        }

        // Normalisation du numéro (ex: +243..., +33..., etc.)
        $normalized = $this->normalizePhoneNumber($phoneNumber);

        try {
            $response = $this->httpClient->request('POST', $this->providerUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'from' => $this->senderName,
                    'to' => $normalized,
                    'text' => $message,
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode >= 200 && $statusCode < 300) {
                $this->logger->info("[SmsSender] SMS envoyé avec succès ✅", [
                    'to' => $normalized,
                    'message' => $message,
                ]);
                return true;
            }

            $this->logger->error("[SmsSender] Échec de l'envoi SMS ❌", [
                'to' => $normalized,
                'status' => $statusCode,
                'response' => $response->getContent(false),
            ]);
            return false;
        } catch (\Throwable $e) {
            $this->logger->error("[SmsSender] Erreur exception", [
                'to' => $normalized,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Normalise le numéro en format international
     */
    private function normalizePhoneNumber(string $phone): string
    {
        $clean = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($clean, '0')) {
            return '+33' . substr($clean, 1);
        }
        if (!str_starts_with($clean, '+')) {
            return '+' . $clean;
        }
        return $clean;
    }
}
