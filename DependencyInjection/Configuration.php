<?php

namespace Tesla\Bundle\ApiKeySecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tesla_api_key_security');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode->children()
            ->arrayNode('in_memory')->canBeDisabled()
            ->children()
            ->arrayNode('users')
            ->prototype('array')
            ->children()
            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('api_key')->isRequired()->end()
            ->arrayNode('roles')->protoType('scalar')->end()->end()
            ->scalarNode('expires')->defaultValue('2099-09-09')->end()
            ->arrayNode('environments')->prototype('scalar')->end();


        return $treeBuilder;
    }
}
