<?php

namespace Syrma\WebContainerBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddServerPass;

class AddServerPassTest extends AbstractAddToRegistryPassTest
{
    /**
     * {@inheritdoc}
     */
    protected function getTagName()
    {
        return AddServerPass::TAG_NAME;
    }

    /**
     * @return string
     */
    protected function getRegistryId()
    {
        return AddServerPass::REGISTRY_ID;
    }

    /**
     * @return CompilerPassInterface
     */
    protected function createPass()
    {
        return new AddServerPass();
    }
}
