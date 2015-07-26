<?php

namespace Syrma\WebContainerBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Syrma\WebContainer\Server\Swoole\SwooleServer;
use Syrma\WebContainer\Util\ErrorPageLoader;
use Syrma\WebContainerBundle\DependencyInjection\Configuration;
use Syrma\WebContainerBundle\Executor\ExecutorFactory;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $conf
     *
     * @return array
     */
    protected function createConfig($conf)
    {
        return (new Processor())->processConfiguration(new Configuration(), array($conf));
    }

    public function testDefalut()
    {
        $config = $this->createConfig(array());

        $this->assertEquals(array(
            'server' => array(
                'default' => SwooleServer::isAvaiable() ? 'swoole' : null,
                'swoole' => array(
                    'enabled' => false,
                    'transformer' => array(
                        'use_server_request' => true,
                        'response_buffer' => 8096,
                    ),
                    'options' => array(),
                ),
            ),
            'request_handler' => array('default' => 'symfony'),
            'exception_handler' => array(
                'error_page' => array(
                    'template_path' => ErrorPageLoader::getDefaultTemplatePath(),
                ),
            ),
            'executor' => array(
                'default' => 'default',
                'executorList' => array(
                    'default' => array(
                        'server' => ExecutorFactory::USE_DEFAULT_KEY,
                        'request_handler' => ExecutorFactory::USE_DEFAULT_KEY,
                        'exception_handler_service' => 'syrma.web_container.exception_handler',
                        'parent_service' => 'syrma.web_container.executor.abstract',
                        'use_factory' => true,
                    ),
                ),
            ),
        ), $config);
    }

    public function testMultiExecutor()
    {
        $config = $this->createConfig(array(
            'executor' => array(
                'executorList' => array(
                    'foo' => array(
                        'server' => 'myOtherServer',
                    ),
                    'bar' => array(
                        'request_handler' => 'myOtherRequestHandler',
                    ),
                ),
            ),
        ));

        $this->assertArrayHasKey('executor', $config);
        $this->assertArrayHasKey('executorList', $config['executor']);
        $this->assertEquals(array(
            'foo' => array(
                'server' => 'myOtherServer',
                'request_handler' => ExecutorFactory::USE_DEFAULT_KEY,
                'exception_handler_service' => 'syrma.web_container.exception_handler',
                'parent_service' => 'syrma.web_container.executor.abstract',
                'use_factory' => true,
            ),
            'bar' => array(
                'server' => ExecutorFactory::USE_DEFAULT_KEY,
                'request_handler' => 'myOtherRequestHandler',
                'exception_handler_service' => 'syrma.web_container.exception_handler',
                'parent_service' => 'syrma.web_container.executor.abstract',
                'use_factory' => true,
            ),

        ), $config['executor']['executorList']);
    }
}
