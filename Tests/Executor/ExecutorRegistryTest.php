<?php

namespace Syrma\WebContainerBundle\Tests\Executor;

use Syrma\WebContainer\Executor;
use Syrma\WebContainerBundle\Executor\ExecutorRegistry;

class ExecutorRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Executor
     */
    protected function createExecutor()
    {
        return $this->getMockBuilder(Executor::class)
                    ->disableOriginalConstructor()
                    ->getMock()
            ;
    }

    public function testSimple()
    {
        $exe1 = $this->createExecutor();
        $exe2 = $this->createExecutor();
        $exe3 = $this->createExecutor();

        $reg = new ExecutorRegistry();
        $reg->add('foo', $exe1, true);

        $this->assertSame($exe1, $reg->get('foo'));
        $this->assertSame($exe1, $reg->getDefault());

        $reg->add('bar', $exe2, true);
        $this->assertSame($exe2, $reg->get('bar'));
        $this->assertSame($exe2, $reg->getDefault());

        $reg->add('foo-bar', $exe3, false);
        $this->assertSame($exe3, $reg->get('foo-bar'));
        $this->assertSame($exe2, $reg->getDefault());

        $this->assertSame($exe1, $reg->get('foo'));
        $this->assertSame($exe2, $reg->get('bar'));
    }

    public function testSingleDefault()
    {
        $exe1 = $exe1 = $this->createExecutor();

        $reg = new ExecutorRegistry();
        $reg->add('foo', $exe1, false);

        $this->assertSame($exe1, $reg->get('foo'));
        $this->assertSame($exe1, $reg->getDefault());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 1
     */
    public function testGetNotExistingService()
    {
        $reg = new ExecutorRegistry();
        $reg->get('foo');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 2
     */
    public function testGetDefaultAndEmpty()
    {
        $reg = new ExecutorRegistry();
        $reg->getDefault();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 3
     */
    public function testGetDefaultAndTooMany()
    {
        $exe1 = $this->createExecutor();
        $exe2 = $this->createExecutor();

        $reg = new ExecutorRegistry();
        $reg->add('foo', $exe1, false);
        $reg->add('bar', $exe2, false);

        $reg->getDefault();
    }
}
