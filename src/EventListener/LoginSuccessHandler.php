<?php

namespace App\EventListener;

use App\Constants\NotificationTypes;
use App\Entity\User;
use App\Exception\NotificationChannelsException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use App\Service\NotificationService;
use Twig\Environment as TwigEnvironment;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;
    private NotificationService $notificationService;
    private RequestStack $requestStack;
    private TwigEnvironment $twig;

    public function __construct(RouterInterface $router, NotificationService $notificationService, RequestStack $requestStack, TwigEnvironment $twig)
    {
        $this->router = $router;
        $this->notificationService = $notificationService;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
    }

    /**
     * @throws Exception
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        /** @var User $user */
        $user = $token->getUser();

        $templatePath = 'notifications/sms/login_notification.txt.twig';
        $templateData = [
            'user' => $user,
            'time' => date('H:i'),
        ];

        try {
            $messageContent = $this->twig->render($templatePath, $templateData);
            $this->notificationService->send($user, $messageContent, [NotificationTypes::SMS]);
        } catch (NotificationChannelsException $exception) {
            $session->getFlashBag()->add('error', 'Failed to send any notification.');
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }
}
