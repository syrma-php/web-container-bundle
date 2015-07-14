<?php

namespace Syrma\WebContainerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collect the taggad service, and setup for ServerRegistry.
 */
class AddServerPass extends AbstractAddToRegistryPass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $regId = 'syrma.web_container.server.registry';
        $tag = 'syrma.web_container.server';

        $this->doProcess($container, $regId, $tag);
    }
}
