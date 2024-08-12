<?php

namespace App\Service\Channel;

use App\Entity\User;

/**
 * Interface for notification channels.
 */
interface NotificationChannelInterface
{
    public function sendNotification(User $user, string $messageContent, array $additionalNotificationData = []): void;
    public function shouldSendNotification(User $user): bool;
    public function getChannelType(): string;
    public function isAdditionalDataProvided(array $additionalNotificationData): bool;
}
