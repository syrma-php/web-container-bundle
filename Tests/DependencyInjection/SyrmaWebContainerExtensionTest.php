<?php

namespace Syrma\WebContainerBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Syrma\WebContainerBundle\DependencyInjection\SyrmaWebContainerExtension;

class SyrmaWebContainerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     *
     * @return ContainerBuilder
     */
    private function buildContainer(array $config)
    {
        $container = new ContainerBuilder();
        $ext = new SyrmaWebContainerExtension();
        $ext->load(array($config), $container);

        return $container;
    }

    public function testEmptyConfiguration()
    {
        $container = $this->buildContainer(array());

        $noExistsIds = array(
            'syrma.web_container.server.swoole.message_transformer',
            'syrma.web_container.server.swoole.options',
            'syrma.web_container.server.swoole',
        );
        foreach ($noExistsIds as $id) {
            $this->assertFalse($container->hasDefinition($id), sprintf('The service(%s) exist in container!', $id));
        }

        $existsIds = array(
            'syrma.web_container.server.registry',
            'syrma.web_container.request_handler.registry',
            'syrma.web_container.request_handler.symfony',
        );
        foreach ($existsIds as $id) {
            $this->assertTrue($container->hasDefinition($id), sprintf('The service(%s) not exist in container!', $id));
        }

        $existsAlias = array(
            'syrma.web_container.psr7.message.factory',
        );
        foreach ($existsAlias as $id) {
            $this->assertTrue($container->hasAlias($id), sprintf('The alias(%s) not exist in container!', $id));
        }
    }

    public function testSwooleServerConfiguration()
    {
        $config = array(
            'server' => array(
                'default' => 'swoole',
                'swoole' => array(
                    'enabled' => true,
                    'transformer' => array(
                        'use_server_request' => true,
                        'response_buffer' => 42,
                    ),
                    'options' => array(
                        'worker_num' => 5,
                        'cpu_affinity_ignore' => array('fake'),
                    ),
                ),
            ),
        );

        $container = $this->buildContainer($config);

        $transDef = $container->findDefinition('syrma.web_container.server.swoole.message_transformer');
        $this->assertTrue($transDef->getArgument(1));
        $this->assertSame(42, $transDef->getArgument(2));

        $optDef = $container->findDefinition('syrma.web_container.server.swoole.options');
        $this->assertSame(array('worker_num' => 5, 'cpu_affinity_ignore' => array('fake')), $optDef->getArgument(0));

        $serverDef = $container->findDefinition('syrma.web_container.server.swoole');
        $tagInfo = $serverDef->getTag('syrma.web_container.server');
        $this->assertSame(array(array('default' => true, 'alias' => 'swoole')), $tagInfo);
    }

    public function testSwooleServerNotDefaultConfiguration()
    {
        $config = array(
            'server' => array(
                'default' => null,
                'swoole' => array(
                    'enabled' => true,
                ),
            ),
        );

        $container = $this->buildContainer($config);

        $serverDef = $container->findDefinition('syrma.web_container.server.swoole');
        $tagInfo = $serverDef->getTag('syrma.web_container.server');
        $this->assertSame(array(array('default' => false, 'alias' => 'swoole')), $tagInfo);
    }

    public function testSymfonyRequestHandler()
    {
        $config = array(
            'request_handler' => array(
                'default' => 'symfony',
            ),
        );

        $container = $this->buildContainer($config);

        $reqHandDef = $container->findDefinition('syrma.web_container.request_handler.symfony');
        $tagInfo = $reqHandDef->getTag('syrma.web_container.request_handler');
        $this->assertSame(array(array('default' => true, 'alias' => 'symfony')), $tagInfo);
    }

    public function testSymfonyRequestHandlerNotDefault()
    {
        $config = array(
            'request_handler' => array(
                'default' => null,
            ),
        );

        $container = $this->buildContainer($config);

        $reqHandDef = $container->findDefinition('syrma.web_container.request_handler.symfony');
        $tagInfo = $reqHandDef->getTag('syrma.web_container.request_handler');
        $this->assertSame(array(array('default' => false, 'alias' => 'symfony')), $tagInfo);
    }
}
