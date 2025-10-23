<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class OverrideCsrfManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('security.csrf.same_origin_token_manager')) {
            $container->removeDefinition('security.csrf.same_origin_token_manager');
        }

        if ($container->hasDefinition('security.csrf.token_manager')) {
            $definition = $container->getDefinition('security.csrf.token_manager');
            $definition->setClass(CsrfTokenManager::class);
            $definition->setArguments([
                $container->getDefinition('security.csrf.token_generator'),
                $container->getDefinition('security.csrf.token_storage'),
            ]);
        }
    }
}
