<?php

namespace App\Controller;

use App\Constants\NotificationTypes;
use App\Entity\User;
use App\Service\NotificationService;
use Exception;
use PhpParser\JsonDecoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class NotificationController extends AbstractController
{

    public function __construct(
        private readonly NotificationService  $notificationService,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    )
    {
    }

    public function sendNotification(string $type, Request $request): JsonResponse
    {
        /** @var $user User */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $csrfToken = $request->headers->get('X-CSRF-Token');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('send-notification', $csrfToken))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $messageContent = $request->request->get('message', 'Test message');

        $notificationChannels = match (strtolower($type)) {
            NotificationTypes::SMS => [NotificationTypes::SMS],
            NotificationTypes::EMAIL => [NotificationTypes::EMAIL],
            default => [NotificationTypes::SMS, NotificationTypes::EMAIL],
        };

        $additionalNotificationData = [
            'subject' => 'Test subject',
        ];

        try {
            $this->notificationService->send(
                user: $user,
                messageContent: $messageContent,
                notificationChannels: $notificationChannels,
                additionalNotificationData: $additionalNotificationData
            );
            return new JsonResponse(['success' => ucfirst($type) . ' sent successfully']);
        } catch (Exception $exception) {
            return new JsonResponse(['error' => 'Failed to send ' . $type . ': ' . $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
