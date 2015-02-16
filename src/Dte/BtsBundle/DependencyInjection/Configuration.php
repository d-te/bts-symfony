<?php

namespace Dte\BtsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dte_bts');

        $rootNode
            ->children()
                ->arrayNode('notification')
                    ->children()
                        ->scalarNode('noreply_email')->end()
                    ->end()
                ->end() // notification
            ->end()
        ;

        return $treeBuilder;
    }
}
