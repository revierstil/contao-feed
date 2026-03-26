<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\EventListener\Kernel;

use Contao\CoreBundle\Security\ContaoCorePermissions;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(RequestEvent::class)]
final class PermissionListener
{

    public function __construct(
        private readonly Security $security,
        private readonly array $userGroups
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (str_contains($request->attributes->getString('_route'), 'revierstil_contao_feed') === false || ($this->userGroups['read'] ?? null) === null) {
            return;
        }

        if($this->security->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, (int) $this->userGroups['read']) === true) {
            return;
        }

        $event->setResponse(new Response('', Response::HTTP_FORBIDDEN));
    }
}