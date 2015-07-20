<?php

namespace Syrma\WebContainerBundle\Tests;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddRequestHandlerPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddServerPass;
use Syrma\WebContainerBundle\SyrmaWebContainerBundle;

class SyrmaWebContainerBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $cont = new ContainerBuilder();
        (new SyrmaWebContainerBundle())->build($cont);

        $this->assertEquals(array(
            new AddRequestHandlerPass(),
            new AddServerPass(),
        ), $cont->getCompilerPassConfig()->getBeforeOptimizationPasses());
    }
}
