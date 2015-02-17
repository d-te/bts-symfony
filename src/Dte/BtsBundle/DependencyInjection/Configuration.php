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
                ->scalarNode('noreply_email')->defaultValue('noreply@dte-bts.dev')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
