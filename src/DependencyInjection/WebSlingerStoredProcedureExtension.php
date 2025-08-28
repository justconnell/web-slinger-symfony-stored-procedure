<?php

namespace WebSlinger\StoredProcedureFactory\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use WebSlinger\StoredProcedureFactory\StoredProcedureFactory;

class WebSlingerStoredProcedureExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        // Set parameters for the StoredProcedureFactory service
        $container->setParameter('webslinger.stored_procedure.hostname', $config['stored_procedure']['hostname']);
        $container->setParameter('webslinger.stored_procedure.username', $config['stored_procedure']['username']);
        $container->setParameter('webslinger.stored_procedure.password', $config['stored_procedure']['password']);
    }

    public function getAlias(): string
    {
        return 'webslinger.stored_procedure';
    }
}