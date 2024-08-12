<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class ExceptionListener
{
    private Environment $twig;
    private RequestStack $requestStack;
    private KernelInterface $kernel;

    public function __construct(Environment $twig, RequestStack $requestStack, KernelInterface $kernel)
    {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->kernel = $kernel;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedHttpException) {
            $response = new Response($this->twig->render('exceptions/error403.html.twig'), 403);
            $event->setResponse($response);
        }
    }
}
