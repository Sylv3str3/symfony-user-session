<?php

namespace UserSessionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('user_session');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('max_sessions_per_user')
                    ->defaultValue(5)
                    ->info('Maximum number of active sessions per user')
                ->end()
                ->integerNode('update_threshold')
                    ->defaultValue(300)
                    ->info('Threshold in seconds before updating lastActiveAt (default: 5 minutes)')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
