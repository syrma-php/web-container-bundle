<?php

namespace Syrma\WebContainerBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractAddToRegistryPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    abstract protected function getTagName();

    /**
     * @return string
     */
    abstract protected function getRegistryId();

    /**
     * @return CompilerPassInterface
     */
    abstract protected function createPass();

    public function testEmpty()
    {
        $cont = new ContainerBuilder();
        $this->assertEquals(array('service_container'), $cont->getServiceIds());

        $this->createPass()->process($cont);
        $this->assertEquals(array('service_container'), $cont->getServiceIds());
    }

    public function testNoTaggedService()
    {
        $cont = new ContainerBuilder();
        $cont->setDefinition($this->getRegistryId(), new Definition(\stdClass::class));

        $this->createPass()->process($cont);

        $regDef = $cont->findDefinition($this->getRegistryId());
        $this->assertEquals(array(), $regDef->getMethodCalls());
    }

    public function testCollect()
    {
        $cont = new ContainerBuilder();
        $cont->setDefinition($this->getRegistryId(), new Definition(\stdClass::class));

        $cont->setDefinition('fake', new Definition(\stdClass::class));

        $srvDef1 = new Definition(\stdClass::class);
        $srvDef1->addTag($this->getTagName(), array('default' => true, 'alias' => 'foo'));
        $cont->setDefinition('srvDef1', $srvDef1);

        $srvDef2 = new Definition(\stdClass::class);
        $srvDef2->addTag($this->getTagName(), array('default' => false, 'alias' => 'bar'));
        $cont->setDefinition('srvDef2', $srvDef2);

        $srvDef3 = new Definition(\stdClass::class);
        $srvDef3->addTag($this->getTagName(), array('alias' => 'foo-bar'));
        $cont->setDefinition('srvDef3', $srvDef3);

        $this->createPass()->process($cont);

        $refDef = $cont->findDefinition($this->getRegistryId());
        $this->assertEquals(array(
            array('add', array('foo', new Reference('srvDef1'), true)),
            array('add', array('bar', new Reference('srvDef2'), false)),
            array('add', array('foo-bar', new Reference('srvDef3'), false)),
        ), $refDef->getMethodCalls());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedException 1
     */
    public function testEmptyAlias()
    {
        $cont = new ContainerBuilder();
        $cont->setDefinition($this->getRegistryId(), new Definition(\stdClass::class));

        $srvDef1 = new Definition(\stdClass::class);
        $srvDef1->addTag($this->getTagName());
        $cont->setDefinition('srvDef1', $srvDef1);

        $this->createPass()->process($cont);
    }
}
