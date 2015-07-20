<?php

namespace Syrma\WebContainerBundle\Tests\Server;

use Syrma\WebContainer\ServerInterface;
use Syrma\WebContainerBundle\Server\ServerRegistry;

class ServerRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $serv1 = $this->getMock(ServerInterface::class);
        $serv2 = $this->getMock(ServerInterface::class);
        $serv3 = $this->getMock(ServerInterface::class);

        $reg = new ServerRegistry();
        $reg->add('foo', $serv1, true);

        $this->assertSame($serv1, $reg->get('foo'));
        $this->assertSame($serv1, $reg->getDefault());

        $reg->add('bar', $serv2, true);
        $this->assertSame($serv2, $reg->get('bar'));
        $this->assertSame($serv2, $reg->getDefault());

        $reg->add('foo-bar', $serv3, false);
        $this->assertSame($serv3, $reg->get('foo-bar'));
        $this->assertSame($serv2, $reg->getDefault());

        $this->assertSame($serv1, $reg->get('foo'));
        $this->assertSame($serv2, $reg->get('bar'));
    }

    public function testSingleDefault()
    {
        $serv1 = $this->getMock(ServerInterface::class);

        $reg = new ServerRegistry();
        $reg->add('foo', $serv1, false);

        $this->assertSame($serv1, $reg->get('foo'));
        $this->assertSame($serv1, $reg->getDefault());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1
     */
    public function testGetNotExistingService()
    {
        $reg = new ServerRegistry();
        $reg->get('foo');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 2
     */
    public function testGetDefaultAndEmpty()
    {
        $reg = new ServerRegistry();
        $reg->getDefault();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 3
     */
    public function testGetDefaultAndTooMany()
    {
        $serv1 = $this->getMock(ServerInterface::class);
        $serv2 = $this->getMock(ServerInterface::class);

        $reg = new ServerRegistry();
        $reg->add('foo', $serv1, false);
        $reg->add('bar', $serv2, false);

        $reg->getDefault();
    }
}
