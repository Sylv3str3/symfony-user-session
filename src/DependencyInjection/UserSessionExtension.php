<?php

namespace UserSessionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class UserSessionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'user_session.max_sessions_per_user',
            $config['max_sessions_per_user']
        );
        
        $container->setParameter(
            'user_session.update_threshold',
            $config['update_threshold']
        );

        $container->setParameter(
            'user_session.entity.class',
            $config['user_session_class']
        );
    }
}
