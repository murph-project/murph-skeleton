<?php

namespace App\Core\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('core');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('site')
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('logo')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('controllers')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('name')
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('action')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                            ->end()
                        ->end()
                        ->arrayNode('pages')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->arrayNode('templates')
                                    ->prototype('array')
                                    ->children()
                                        ->scalarNode('name')
                                            ->cannotBeEmpty()
                                        ->end()
                                        ->scalarNode('file')
                                            ->cannotBeEmpty()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
