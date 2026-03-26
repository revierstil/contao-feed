<?php

declare(strict_types=1);

namespace Revierstil\ContaoFeed\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('revierstil_contao_feed');
        $root        = $treeBuilder->getRootNode();

        $root->append($this->getSortingSettings());
        $root->append($this->getFilterSettings());
        $root->append($this->getImageSizeSettings());
        $root->append($this->getUserGroupSettings());
        $root->append($this->uploadPathSettings());
        return $treeBuilder;
    }

    private function getImageSizeSettings(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('imageSizes');

        $node
            ->children()
            ->integerNode('avatarSize')->defaultValue(null)->end()
            ->integerNode('imageSize')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    private function getUserGroupSettings(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('userGroups');

        $node
            ->children()
            ->scalarNode('read')->defaultValue(null)->end()
            ->end();

        return $node;
    }

    private function getSortingSettings(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('sorting');

        $node
            ->arrayPrototype()
            ->children()
            ->scalarNode('translationKey')->defaultValue('')->end()
            ->scalarNode('value')->defaultValue('')->end()
            ->booleanNode('default')->defaultValue(false)->end()
            ->end()
            ->end()
            ->end();

        return $node;
    }

    private function getFilterSettings(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('filter');

        $node
            ->arrayPrototype()
            ->children()
            ->scalarNode('optionGroup')->defaultValue('')->end()
            ->end()
            ->end()
            ->end();

        return $node;
    }

    private function uploadPathSettings(): ScalarNodeDefinition
    {
        $node = new ScalarNodeDefinition('uploadPath');
        $node->defaultValue('uploads/contao-feed');

        return $node;
    }
}
