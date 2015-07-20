<?php

namespace Syrma\WebContainerBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddRequestHandlerPass;

class AddRequestHandlerPassTest extends AbstractAddToRegistryPassTest
{
    /**
     * {@inheritdoc}
     */
    protected function getTagName()
    {
        return AddRequestHandlerPass::TAG_NAME;
    }

    /**
     * @return string
     */
    protected function getRegistryId()
    {
        return AddRequestHandlerPass::REGISTRY_ID;
    }

    /**
     * @return CompilerPassInterface
     */
    protected function createPass()
    {
        return new AddRequestHandlerPass();
    }
}
