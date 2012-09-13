<?php

namespace Mazenovi\WsseAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mazenovi\WsseAuthBundle\DependencyInjection\Security\Factory\WsseFactory;

class MazenoviWsseAuthBundle extends Bundle
{
	public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WsseFactory());
    }
}
