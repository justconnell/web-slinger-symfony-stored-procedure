<?php

namespace WebSlinger\StoredProcedureFactory\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('webslinger');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('stored_procedure')
                    ->children()
                        ->scalarNode('hostname')
                            ->defaultValue('')
                            ->info('Database hostname')
                        ->end()
                        ->scalarNode('username')
                            ->defaultValue('')
                            ->info('Database username')
                        ->end()
                        ->scalarNode('password')
                            ->defaultValue('')
                            ->info('Database password')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}