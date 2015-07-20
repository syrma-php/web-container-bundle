<?php

namespace Syrma\WebContainerBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Collect the taggad service, and setup for Reqistry.
 */
abstract class AbstractAddToRegistryPass implements CompilerPassInterface
{
    const EXT_CODE_EMPTY_ALIAS = 1;

    /**
     * @param ContainerBuilder $container
     * @param string           $regId
     * @param string           $tag
     */
    protected function doProcess(ContainerBuilder $container, $regId, $tag)
    {
        if (false === $container->hasDefinition($regId)) {
            return;
        }

        $definition = $container->findDefinition($regId);

        foreach ($container->findTaggedServiceIds($tag) as $id => $attr) {
            $isDefault = !empty($attr[0]['default']);

            if (empty($attr[0]['alias'])) {
                throw new InvalidConfigurationException(sprintf(
                    'The tag(%s) of service(%s) not contains the "alias" attribute!',
                    $tag,
                    $id
                ), self::EXT_CODE_EMPTY_ALIAS);
            }

            $definition->addMethodCall('add', array(
                $attr[0]['alias'],
                new Reference($id),
                $isDefault,
            ));
        }
    }
}
