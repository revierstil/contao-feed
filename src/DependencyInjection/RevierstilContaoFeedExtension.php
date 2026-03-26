<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class RevierstilContaoFeedExtension extends Extension
{
    /**
     * @param array<array<mixed>> $configs
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('revierstil_contao_feed.sorting', $config['sorting']);
        $container->setParameter('revierstil_contao_feed.filter', $config['filter']);
        $container->setParameter('revierstil_contao_feed.imageSizes', $config['imageSizes']);
        $container->setParameter('revierstil_contao_feed.uploadPath', $config['uploadPath']);
        $container->setParameter('revierstil_contao_feed.userGroups', $config['userGroups']);
    }
}
