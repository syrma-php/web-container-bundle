<?php

namespace Syrma\WebContainerBundle\Tests\RequestHandler;

use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainerBundle\RequestHandler\RequestHandlerRegistry;

class RequestHandlerRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $reqHand1 = $this->getMock(RequestHandlerInterface::class);
        $reqHand2 = $this->getMock(RequestHandlerInterface::class);
        $reqHand3 = $this->getMock(RequestHandlerInterface::class);

        $reg = new RequestHandlerRegistry();
        $reg->add('foo', $reqHand1, true);

        $this->assertSame($reqHand1, $reg->get('foo'));
        $this->assertSame($reqHand1, $reg->getDefault());

        $reg->add('bar', $reqHand2, true);
        $this->assertSame($reqHand2, $reg->get('bar'));
        $this->assertSame($reqHand2, $reg->getDefault());

        $reg->add('foo-bar', $reqHand3, false);
        $this->assertSame($reqHand3, $reg->get('foo-bar'));
        $this->assertSame($reqHand2, $reg->getDefault());

        $this->assertSame($reqHand1, $reg->get('foo'));
        $this->assertSame($reqHand2, $reg->get('bar'));
    }

    public function testSingleDefault()
    {
        $reqHand1 = $this->getMock(RequestHandlerInterface::class);

        $reg = new RequestHandlerRegistry();
        $reg->add('foo', $reqHand1, true);

        $this->assertSame($reqHand1, $reg->get('foo'));
        $this->assertSame($reqHand1, $reg->getDefault());
    }
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1
     */
    public function testGetNotExistingService()
    {
        $reg = new RequestHandlerRegistry();
        $reg->get('foo');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 2
     */
    public function testGetDefaultAndEmpty()
    {
        $reg = new RequestHandlerRegistry();
        $reg->getDefault();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 3
     */
    public function testGetDefaultAndTooMany()
    {
        $reqHand1 = $this->getMock(RequestHandlerInterface::class);
        $reqHand2 = $this->getMock(RequestHandlerInterface::class);

        $reg = new RequestHandlerRegistry();
        $reg->add('foo', $reqHand1, false);
        $reg->add('bar', $reqHand2, false);

        $reg->getDefault();
    }
}
