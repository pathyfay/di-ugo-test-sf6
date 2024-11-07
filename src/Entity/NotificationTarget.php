<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class NotificationTarget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification_read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Notification::class, inversedBy: 'targets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Notification $notification = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification_read'])]
    private ?User $user = null;

    #[ORM\Column(length: 20)]
    #[Groups(['notification_read'])]
    private string $channel; // 'email' ou 'sms'

    #[ORM\Column(type: 'boolean')]
    #[Groups(['notification_read'])]
    private bool $delivered = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deliveredAt = null;

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): void { $this->user = $user; }

    public function getChannel(): string { return $this->channel; }
    public function setChannel(string $channel): void { $this->channel = $channel; }

    public function isDelivered(): bool { return $this->delivered; }
    public function setDelivered(bool $delivered): void { $this->delivered = $delivered; }

    public function getDeliveredAt(): ?\DateTimeImmutable { return $this->deliveredAt; }
    public function setDeliveredAt(?\DateTimeImmutable $deliveredAt): void { $this->deliveredAt = $deliveredAt; }

    public function setNotification(?Notification $notification): void { $this->notification = $notification; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user?->getId(),
            'channel' => $this->channel,
            'delivered' => $this->delivered,
            'deliveredAt' => $this->deliveredAt?->format('Y-m-d H:i:s'),
        ];
    }
}