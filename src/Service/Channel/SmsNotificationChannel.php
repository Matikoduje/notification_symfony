<?php

namespace App\Service\Channel;

use App\Constants\NotificationTypes;
use App\Entity\User;
use App\Exception\SmsServiceException;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

/**
 * Notification channel for sending SMS notifications.
 */
readonly class SmsNotificationChannel implements NotificationChannelInterface
{

    public function __construct(
        private TexterInterface $texter
    )
    {
    }

    /**
     * Sends an SMS notification to the user.
     *
     * @param User $user The user to send the notification to.
     * @param string $messageContent The content of the message.
     *
     * @throws SmsServiceException If the SMS could not be sent.
     */
    public function sendNotification(User $user, string $messageContent, array $additionalNotificationData = []): void
    {
        if (!$this->isAdditionalDataProvided($additionalNotificationData)) {
            throw new SmsServiceException('SMS data not provided');
        }

        if (!$this->shouldSendNotification($user)) {
            return;
        }

        $recipient = $user->getFullPhoneNumber();
        $sms = new SmsMessage($recipient, $messageContent);

        try {
            $this->texter->send($sms);
        } catch (TransportExceptionInterface $e) {
            throw new SmsServiceException('Failed to send SMS to ' . $recipient . ': ' . $e->getMessage());
        }
    }

    /**
     * Checks if the user has consented to SMS notifications and has a phone number.
     *
     * @param User $user The user to check.
     *
     * @return bool True if the user has consented to SMS notifications and has a phone number, false otherwise.
     */
    public function shouldSendNotification(User $user): bool
    {
        return $user->isSmsNotificationConsent() && $user->getFullPhoneNumber() !== null;
    }

    /**
     * Returns the channel type.
     *
     * @return string The channel type.
     */
    public function getChannelType(): string
    {
        return NotificationTypes::SMS;
    }

    public function isAdditionalDataProvided(array $additionalNotificationData): bool
    {
        return true;
    }
}
