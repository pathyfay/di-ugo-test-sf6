<?php

namespace App\Command;

use App\Entity\User;
use App\Enum\NotificationTypeEnum;
use App\Repository\UserRepository;
use App\Service\NotificationManager;
use App\Service\NotificationManagerService;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:notifications:send-birthdays', description: 'Envoie les notifications d’anniversaire à la filiation')]
class SendBirthdayNotificationsCommand extends Command
{
    public function __construct(
        private readonly UserRepository      $userRepo,
        private readonly NotificationManagerService $notificationManager,
        private readonly string              $tz = 'Europe/Paris',
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('channels', null, InputOption::VALUE_REQUIRED, 'Canaux séparés par des virgules', 'email,sms')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'N’enregistre ni n’envoie, affiche seulement');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new DateTimeImmutable('now', new DateTimeZone($this->tz));
        $channels = array_filter(array_map('trim', explode(',', (string)$input->getOption('channels'))));
        $dryRun = (bool)$input->getOption('dry-run');

        $birthdayUsers = $this->userRepo->findUsersHavingBirthdayOn($now);

        if (empty($birthdayUsers)) {
            $output->writeln('<info>Aucun anniversaire aujourd’hui.</info>');
            return Command::SUCCESS;
        }

        foreach ($birthdayUsers as $user) {
            \assert($user instanceof User);
            $title = '🎂 Joyeux anniversaire !';
            $message = sprintf("Aujourd’hui (%s), c’est l’anniversaire de %s %s.",
                $now->format('d/m'),
                $user->getFirstName(),
                $user->getLastName()
            );

            if ($dryRun) {
                $output->writeln(sprintf('[DRY-RUN] Notif %s -> %s (%s)', implode('+', $channels), $user->getEmail(), $user->getId()));
                continue;
            }

            $notif = $this->notificationManager->createNotification($user, NotificationTypeEnum::BIRTHDAY, $title, $message, $channels);
            $output->writeln(sprintf('<info>Notification #%d créée pour %s.</info>', $notif->getId(), $user->getEmail()));
        }

        if (!$dryRun) {
            $this->notificationManager->sendPendingNotifications();
            $output->writeln('<comment>Notifications envoyées.</comment>');
        }

        return Command::SUCCESS;
    }
}
