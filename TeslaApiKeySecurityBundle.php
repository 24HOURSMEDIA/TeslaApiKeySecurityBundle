<?php

namespace Tesla\Bundle\ApiKeySecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tesla\Bundle\ApiKeySecurityBundle\Security\Factory\ApiKeySecurityFactory;
use Tesla\Bundle\ApiKeySecurityBundle\Security\InMemoryApiUserProvider;
use Tesla\Bundle\ApiKeySecurityBundle\Security\InMemoryApiUserFactory;

class TeslaApiKeySecurityBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new ApiKeySecurityFactory());

        // $extension->addUserProviderFactory(new InMemoryApiUserFactory());
    }
}
