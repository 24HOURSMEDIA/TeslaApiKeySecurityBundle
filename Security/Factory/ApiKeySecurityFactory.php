<?php
// $Id:  $
// $HeadURL:  $
/**
 * Created by JetBrains PhpStorm.
 * User: eapbachman
 * Date: 25/10/13
 * Time: 18:52
 * To change this template use File | Settings | File Templates.
 */

namespace Tesla\Bundle\ApiKeySecurityBundle\Security\Factory;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;


class ApiKeySecurityFactory implements SecurityFactoryInterface
{

    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.api_key.' . $id;


        //$userProvider = 'api_key.api_user_provider';

        $userProvider = $config['service'];
        //if (!$container->has($userProvider)) {
        //     throw new InvalidConfigurationException('The service defined in api_key_security does not exist');
        // }

        $container
            ->setDefinition($providerId, new DefinitionDecorator('api_key.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.api_key.' . $id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('api_key.security.authentication.listener'));
        $listener->replaceArgument(2, $config['methods']);
        $listener->replaceArgument(3, $config['fields']);


        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'tesla_api_key_security';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
            ->scalarNode('service')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('methods')->prototype('scalar')->end()->end()
            ->arrayNode('fields')->children()
            ->scalarNode('header')->defaultValue('x-api-key')->end()
            ->scalarNode('request_get')->defaultValue('x-api-key')->end()

            ->end();
    }
}