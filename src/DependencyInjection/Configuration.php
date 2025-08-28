<?php

namespace WebSlinger\StoredProcedureFactory\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('web_slinger');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('stored_procedure')
                    ->children()
                        ->scalarNode('hostname')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Database hostname')
                        ->end()
                        ->scalarNode('username')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Database username')
                        ->end()
                        ->scalarNode('password')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Database password')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}