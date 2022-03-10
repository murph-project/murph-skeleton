<?php

namespace App\Core\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $defaultMimetypes = [
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/gif',
            'image/svg+xml',
            'video/mp4',
            'audio/mpeg3',
            'audio/x-mpeg-3',
            'multipart/x-zip',
            'multipart/x-gzip',
            'application/pdf',
            'application/ogg',
            'application/zip',
            'application/rar',
            'application/x-rar-compressed',
            'application/x-zip-compressed',
            'application/tar',
            'application/x-tar',
            'application/x-bzip',
            'application/x-bzip2',
            'application/x-gzip',
            'application/octet-stream',
            'application/msword',
            'text/plain',
            'text/css',
        ];

        $defaultLocked = [
            '%kernel.project_dir%/public/uploads',
        ];

        $treeBuilder = new TreeBuilder('core');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('site')
                    ->children()
                        ->scalarNode('name')
                            ->defaultValue('Murph')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('logo')
                            ->defaultValue('build/images/core/logo.svg')
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
                        ->arrayNode('security')
                            ->children()
                                ->arrayNode('roles')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('name')
                                                ->cannotBeEmpty()
                                            ->end()
                                            ->scalarNode('role')
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
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
                ->end()
                ->arrayNode('file_manager')
                    ->children()
                        ->arrayNode('mimes')
                            ->scalarPrototype()
                            ->end()
                            ->defaultValue($defaultMimetypes)
                        ->end()
                        ->scalarNode('path')
                            ->defaultValue('%kernel.project_dir%/public/uploads')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('path_uri')
                            ->defaultValue('/uploads')
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('path_locked')
                            ->scalarPrototype()
                            ->end()
                            ->defaultValue($defaultLocked)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
