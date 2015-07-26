<?php

namespace Syrma\WebContainerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collect the tagged service, and setup for ExecutorRegistry.
 */
class AddExecutorPass extends AbstractAddToRegistryPass
{
    const REGISTRY_ID = 'syrma.web_container.executor.registry';
    const TAG_NAME = 'syrma.web_container.executor';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->doProcess($container, self::REGISTRY_ID, self::TAG_NAME);
    }
}
