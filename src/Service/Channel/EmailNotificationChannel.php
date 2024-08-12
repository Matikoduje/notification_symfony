<?php

namespace App\Service\Channel;

use App\Constants\NotificationTypes;
use App\Entity\User;
use App\Exception\EmailServiceException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class EmailNotificationChannel implements NotificationChannelInterface
{

    public function __construct(
        private MailerInterface $mailer
    )
    {}

    /**
     * @throws EmailServiceException
     */
    public function sendNotification(User $user, string $messageContent, array $additionalNotificationData = []): void
    {
        if (!$this->isAdditionalDataProvided($additionalNotificationData)) {
            throw new EmailServiceException('Subject not provided');
        }

        if (!$this->shouldSendNotification($user)) {
            return;
        }

        $recipient = $user->getEmail();
        $email = (new Email())
            ->to($recipient)
            ->subject($additionalNotificationData['subject'])
            ->text($messageContent);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new EmailServiceException('Failed to send email to ' . $recipient . ': ' . $e->getMessage());
        }
    }

    public function shouldSendNotification(User $user): bool
    {
        return $user->isEmailNotificationConsent();
    }

    public function getChannelType(): string
    {
        return NotificationTypes::EMAIL;
    }

    public function isAdditionalDataProvided(array $additionalNotificationData): bool
    {
        return array_key_exists('subject', $additionalNotificationData);
    }
}
