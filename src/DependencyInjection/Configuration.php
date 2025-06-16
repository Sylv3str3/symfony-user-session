<?php

namespace UserSessionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use UserSessionBundle\Entity\UserSession;
use UserSessionBundle\Model\AbstractUserSession;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('user_session');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('user_session_class')
                    ->defaultValue(UserSession::class)
                    ->info('The User Session entity class to use')
                    ->validate()
                        ->ifTrue(function ($className) {
                            return !is_subclass_of($className, AbstractUserSession::class);
                        })
                        ->thenInvalid('The user_session_class must extend UserSessionBundle\Model\AbstractUserSession')
                    ->end()
                ->end()
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
