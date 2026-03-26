<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\ContaoManager;

use BoelterIO\DoctrineDBALQueryFilter\BoelterIODoctrineDBAlQueryFilterBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Revierstil\ContaoFeed\RevierstilContaoFeedBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

final class Plugin implements BundlePluginInterface, ConfigPluginInterface, DependentPluginInterface, RoutingPluginInterface
{
    /**
     * @return list<ConfigInterface>
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            (new BundleConfig(RevierstilContaoFeedBundle::class))->setLoadAfter([
                ContaoCoreBundle::class,
                BoelterIODoctrineDBAlQueryFilterBundle::class,
            ]),
            (new BundleConfig(BoelterIODoctrineDBAlQueryFilterBundle::class)),
        ];
    }

    public function getPackageDependencies(): array
    {
        return ['contao/core-bundle'];
    }


    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig)
    {
        $loader->load(__DIR__ . '/../../config/options.yaml');
        $loader->load(__DIR__ . '/../../config/services.yaml');
        $loader->load(__DIR__ . '/../../config/listeners.yaml');
        $loader->load(__DIR__ . '/../../config/repositories.yaml');
        $loader->load(__DIR__ . '/../../config/elements.yaml');
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): RouteCollection|null
    {
        $loader = $resolver->resolve(__DIR__ . '/../../config/routes.yaml');
        if (! $loader) {
            return null;
        }

        $routes = $loader->load(__DIR__ . '/../../config/routes.yaml');
        assert($routes === null || $routes instanceof RouteCollection);

        return $routes;
    }
}
