<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\EventListener\Kernel;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[AsEventListener(ResponseEvent::class)]
final class RequestTokenListener
{

    public function __construct(
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly string $tokenName
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request  = $event->getRequest();

        if($request->getMethod() !== 'POST' || str_contains($request->attributes->getString('_route'), 'revierstil_contao_feed') === false) {
            return;
        }

        if ($response->headers->has('X-Contao-Request-Token')) {
            return;
        }
        $response->headers->set('Access-Control-Expose-Headers', 'X-Contao-Request-Token');
        $response->headers->set('X-Contao-Request-Token', $this->tokenManager->getToken($this->tokenName)->getValue());
    }
}