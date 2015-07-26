<?php

namespace Syrma\WebContainerBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddExecutorPass;

class AddExecutorPassTest extends AbstractAddToRegistryPassTest
{
    /**
     * {@inheritdoc}
     */
    protected function getTagName()
    {
        return AddExecutorPass::TAG_NAME;
    }

    /**
     * @return string
     */
    protected function getRegistryId()
    {
        return AddExecutorPass::REGISTRY_ID;
    }

    /**
     * @return CompilerPassInterface
     */
    protected function createPass()
    {
        return new AddExecutorPass();
    }
}
