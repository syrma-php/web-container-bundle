<?php

namespace Syrma\WebContainerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collect the taggad service, and setup for RequestHandler.
 */
class AddRequestHandlerPass extends AbstractAddToRegistryPass
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $regId = 'syrma.web_container.request_handler.registry';
        $tag = 'syrma.web_container.request_handler';

        $this->doProcess($container, $regId, $tag);
    }
}
