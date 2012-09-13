<?php
namespace Mazenovi\WsseAuthBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class WsseFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.wsse.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('wsse.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProvider))
            ->replaceArgument(1, $config['nonces_path'])
            ->replaceArgument(2, $config['lifetime']);

        $listenerId = 'security.firewall.listener.wsse.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('wsse.security.firewall.listener'));

        $container->setParameter('security.authentication.login.listener.wsse.nonces_path', $config['nonces_path']);
        $container->setParameter('security.authentication.login.listener.wsse.lifetime', $config['lifetime']);
        $container->setParameter('security.authentication.login.listener.wsse.protected_urls', $config['protected_urls']);
        
        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'wsse';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
        ->children()
        ->scalarNode('lifetime')->defaultValue(300)->end()
        ->scalarNode('nonces_path')->defaultValue('%kernel.cache_dir%/security/nonces')->end()
        ->arrayNode('protected_urls')
            ->requiresAtLeastOneElement()
            ->prototype('array')
                ->children()
                    ->scalarNode('pattern')
                        ->isRequired(true)
                    ->end()
                    ->arrayNode('methods')
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }
}