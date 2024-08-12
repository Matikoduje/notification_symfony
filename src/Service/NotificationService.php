<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\NotificationChannelsException;
use Exception;

class NotificationService
{
    private array $channels = [];
    private ?bool $status = null;

    public function __construct(iterable $channels)
    {
        foreach ($channels as $channel) {
            $this->channels[] = $channel;
        }
    }

    /**
     * Sends a notification to the user via the specified channels.
     *
     * @param User $user The user to whom the notification will be sent.
     * @param string $messageContent The content of the message to be sent.
     * @param array $notificationChannels The channels through which the notification will be sent.
     * @param array $additionalNotificationData Additional data required for sending the notification.
     *
     * @throws NotificationChannelsException If all notification channels fail.
     */
    public function send(User $user, string $messageContent, array $notificationChannels, array $additionalNotificationData = []): void
    {
        foreach ($this->channels as $channel) {
            if (in_array($channel->getChannelType(), $notificationChannels, true) === false) {
                continue;
            }
            try {
                $channel->sendNotification($user, $messageContent, $additionalNotificationData);
                $this->status = true;
            } catch (Exception $exception) {
                continue;
            }
        }

        if ($this->status === null) {
            throw new NotificationChannelsException('All notification channels failed');
        }
    }
}
