<?php

namespace Syrma\WebContainerBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Syrma\WebContainerBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $conf
     *
     * @return array
     */
    protected function createConfig($conf)
    {
        return (new Processor())->processConfiguration(new Configuration(), $conf);
    }

    public function testDefalut()
    {
        $config = $this->createConfig(array());

        $this->assertEquals(array(
            'server' => array(
                'default' => class_exists('\swoole_http_server') ? 'swoole' : null, // @TODO ServerInterface::isAvaiable
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
        ), $config);
    }
}
