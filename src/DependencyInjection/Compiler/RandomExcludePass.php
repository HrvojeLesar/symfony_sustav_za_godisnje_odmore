<?php

namespace App\DependencyInjection\Compiler;

use App\Entity\Role;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RandomExcludePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds('app.exclude_role_tag');

        $roles = [
            Role::Admin,
            Role::TeamLead,
            Role::ProjectLead,
        ];

        foreach ($taggedServices as $id => $tags) {
            $definiton = $container->getDefinition($id);
            $idx = array_rand($roles);
            $definiton->addMethodCall('excludeRole', [$roles[$idx]]);
        }
    }
}
