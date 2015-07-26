<?php

namespace Syrma\WebContainerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collect the tagged service, and setup for RequestHandlerRegistry.
 */
class AddRequestHandlerPass extends AbstractAddToRegistryPass
{
    const REGISTRY_ID = 'syrma.web_container.request_handler.registry';
    const TAG_NAME = 'syrma.web_container.request_handler';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->doProcess($container, self::REGISTRY_ID, self::TAG_NAME);
    }
}
