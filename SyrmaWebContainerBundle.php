<?php

namespace Syrma\WebContainerBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddExecutorPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddRequestHandlerPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddServerPass;

/**
 * Bundle of Syrma webcontainer.
 */
class SyrmaWebContainerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddRequestHandlerPass());
        $container->addCompilerPass(new AddServerPass());
        $container->addCompilerPass(new AddExecutorPass());
    }
}
