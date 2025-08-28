<?php

namespace WebSlinger\StoredProcedureFactory\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use WebSlinger\StoredProcedureFactory\StoredProcedureFactory;

class WebSlingerStoredProcedureExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        // Set parameters for the StoredProcedureFactory service
        $storedProcConfig = $config['stored_procedure'] ?? [];
        $container->setParameter('webslinger.stored_procedure.hostname', $storedProcConfig['hostname'] ?? '');
        $container->setParameter('webslinger.stored_procedure.username', $storedProcConfig['username'] ?? '');
        $container->setParameter('webslinger.stored_procedure.password', $storedProcConfig['password'] ?? '');
    }

    public function getAlias(): string
    {
        return 'webslinger';
    }

    public function prepend(ContainerBuilder $container): void
    {
        // Add stored_procedure config to the webslinger tree
        $container->prependExtensionConfig('webslinger', [
            'stored_procedure' => [
                'hostname' => '%env(WEB_SLINGER_SP_HOST)%',
                'username' => '%env(WEB_SLINGER_SP_USERNAME)%',
                'password' => '%env(WEB_SLINGER_SP_PASSWORD)%'
            ]
        ]);
    }
}