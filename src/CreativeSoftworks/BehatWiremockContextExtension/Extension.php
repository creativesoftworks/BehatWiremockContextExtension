<?php

namespace CreativeSoftworks\BehatWiremockContextExtension;

use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Extension implements ExtensionInterface
{
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('wiremock_base_url')->isRequired()->end()
                ->scalarNode('wiremock_mappings_path')->isRequired()->end()
                ->arrayNode('default_mappings')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('mapping')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    public function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter('wiremock_context.default_mappings', $config['default_mappings']);
        $container->setParameter('wiremock_context.base_url', $config['wiremock_base_url']);
        $container->setParameter('wiremock_context.mappings_path', $config['wiremock_mappings_path']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/services'));
        $loader->load('core.yml');  
    }

    public function getCompilerPasses()
    {
        return array();
    }

}
