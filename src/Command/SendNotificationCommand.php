<?php

namespace App\Command;

use App\Constants\NotificationTypes;
use App\Entity\User;
use App\Exception\NotificationChannelsException;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-notification',
    description: 'Send email notification to specified user',
)]
class SendNotificationCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private NotificationService $notificationService;

    public function __construct(EntityManagerInterface $entityManager, NotificationService $notificationService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Sends a notification to a user based on their email address.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email address of the user to whom the notification should be sent');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error(sprintf('No user found with email address "%s".', $email));
            return Command::FAILURE;
        }

        $messageContent = 'Test command message';
        $notificationChannels = [NotificationTypes::EMAIL];

        try {
            $this->notificationService->send(
                user: $user,
                messageContent: $messageContent,
                notificationChannels: $notificationChannels,
                additionalNotificationData: ['subject' => 'Test command subject']
            );
            $io->success(sprintf('Notification of type "%s" sent to user with email address "%s".', NotificationTypes::EMAIL, $email));
            return Command::SUCCESS;
        } catch (Exception $exception) {
            if ($exception instanceof NotificationChannelsException) {
                $io->error('All notification channels failed');
            } else {
                $io->error('An error occurred while sending the notification: ' . $exception->getMessage());
            }
            return Command::FAILURE;
        }
    }
}
