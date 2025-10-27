<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

/**
 * Forces Symfony to use the classic CsrfTokenManager
 * instead of the SameOriginCsrfTokenManager introduced in newer versions.
 */

class OverrideCsrfManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
         // Remove the same-origin CSRF manager if it's registered
        if ($container->hasDefinition('security.csrf.same_origin_token_manager')) {
            $container->removeDefinition('security.csrf.same_origin_token_manager');
        }
       
        // Replace the token manager with the classic CsrfTokenManager
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
