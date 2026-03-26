<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed;

use Revierstil\ContaoFeed\DependencyInjection\RevierstilContaoFeedExtension;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class RevierstilContaoFeedBundle extends AbstractBundle
{
    public function getContainerExtension(): RevierstilContaoFeedExtension
    {
        return new RevierstilContaoFeedExtension();
    }
}
