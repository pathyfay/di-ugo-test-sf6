<?php

namespace App\Entity;

use App\Enum\NotificationChannel;
use App\Enum\NotificationType;
use App\Enum\NotificationTypeEnum;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['notification_read', 'notification_write'])]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['notification_read', 'notification_write'])]
    private ?string $message = null;

    #[ORM\Column(length: 50, enumType: NotificationTypeEnum::class)]
    #[Groups(['notification_read', 'notification_write'])]
    private NotificationTypeEnum $type;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    #[Groups(['notification_read', 'notification_write'])]
    private ?array $channels = []; // ['email', 'sms']

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['notification_read'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['notification_read'])]
    private ?DateTimeImmutable $scheduledAt = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['notification_read'])]
    private bool $sent = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification_read', 'notification_write'])]
    private ?User $createdBy = null; // qui a déclenché la notif

    #[ORM\OneToMany(mappedBy: 'notification', targetEntity: NotificationTarget::class, cascade: ['persist', 'remove'])]
    #[Groups(['notification_read'])]
    private Collection $targets;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->targets = new ArrayCollection();
    }

    // --- Getters / Setters ---

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $message): void { $this->message = $message; }

    public function getType(): NotificationType { return $this->type; }
    public function setType(NotificationType $type): void { $this->type = $type; }

    public function getChannels(): ?array { return $this->channels; }
    public function setChannels(?array $channels): void { $this->channels = $channels; }

    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }

    public function getScheduledAt(): ?DateTimeImmutable { return $this->scheduledAt; }
    public function setScheduledAt(?DateTimeImmutable $scheduledAt): void { $this->scheduledAt = $scheduledAt; }

    public function isSent(): bool { return $this->sent; }
    public function setSent(bool $sent): void { $this->sent = $sent; }

    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function setCreatedBy(?User $user): void { $this->createdBy = $user; }

    public function getTargets(): Collection { return $this->targets; }

    public function addTarget(NotificationTarget $target): void
    {
        if (!$this->targets->contains($target)) {
            $this->targets->add($target);
            $target->setNotification($this);
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type->value,
            'channels' => $this->channels,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'scheduledAt' => $this->scheduledAt?->format('Y-m-d H:i:s'),
            'sent' => $this->sent,
            'createdBy' => $this->createdBy?->getId(),
            'targets' => array_map(fn($t) => $t->toArray(), $this->targets->toArray()),
        ];
    }
}