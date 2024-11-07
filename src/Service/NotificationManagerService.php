<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\NotificationTarget;
use App\Entity\User;
use App\Enum\NotificationTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NotificationManagerService
{
    public function __construct(
        private EntityManagerInterface $em,
        private FamilyManagerService $familyManager,
        private SmsSenderService $smsSender,
        private MailerService $mailer
    ) {}

    /**
     * Crée une notification pour un anniversaire
     */
    public function createNotification(User $user, NotificationTypeEnum $type, string $title, string $message, array $channels = ['email']): Notification
    {
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setMessage($message);
        $notification->setType($type->value);
        $notification->setChannels($channels);
        $notification->setCreatedBy($user);

        $targets = $this->familyManager->getFullFamilyTree($user);
        $users = array_merge(
            array_map(fn($r) => $r['user2'] ?? null, $targets['relations']),
            $targets['children'],
            $targets['parents']
        );

        foreach (array_filter($users) as $u) {
            foreach ($channels as $ch) {
                $target = new NotificationTarget();
                $target->setUser($u);
                $target->setChannel($ch);
                $notification->addTarget($target);
            }
        }

        $this->em->persist($notification);
        $this->em->flush();

        return $notification;
    }

    /**
     * Envoie les notifications programmées ou non encore envoyées.
     */
    public function sendPendingNotifications(): void
    {
        $repo = $this->em->getRepository(Notification::class);
        $pending = $repo->findBy(['sent' => false]);

        foreach ($pending as $notif) {
            foreach ($notif->getTargets() as $target) {
                if ($target->getChannel() === 'sms') {
                    $this->smsSender->send($target->getUser()->getPhone(), $notif->getMessage());
                } elseif ($target->getChannel() === 'email') {
                    $this->mailer->send($target->getUser()->getEmail(), $notif->getTitle(), $notif->getMessage());
                }

                $target->setDelivered(true);
                $target->setDeliveredAt(new \DateTimeImmutable());
            }

            $notif->setSent(true);
            $this->em->flush();
        }
    }

    #[Route('/test-sms', name: 'test_sms')]
    public function testSms(SmsSenderService $smsSender): Response
    {
        $smsSender->send('+33625646992', 'Hello Pathy 👋 test de SMS via FTK TECH!');
        return new Response('SMS envoyé (ou logué)');
    }
}