<?php

namespace Syrma\WebContainerBundle\Tests\Executor;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Syrma\WebContainer\ExceptionHandlerInterface;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerInterface;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddRequestHandlerPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddServerPass;
use Syrma\WebContainerBundle\Executor\ExecutorFactory;
use Syrma\WebContainerBundle\RequestHandler\RequestHandlerRegistry;
use Syrma\WebContainerBundle\Server\ServerRegistry;

class ExecutorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServerRegistry
     */
    protected $serverRegistry;

    /**
     * @var RequestHandlerRegistry
     */
    protected $requestHandlerRegistry;

    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->serverRegistry = new ServerRegistry();
        $this->requestHandlerRegistry = new RequestHandlerRegistry();

        $this->container = new Container();
        $this->container->set(AddServerPass::REGISTRY_ID, $this->serverRegistry);
        $this->container->set(AddRequestHandlerPass::REGISTRY_ID, $this->requestHandlerRegistry);
    }

    /**
     * @return ExecutorFactory
     */
    protected function createFactory()
    {
        return new ExecutorFactory(
            $this->container,
            $this->serverRegistry,
            $this->requestHandlerRegistry,
            ExecutorFakeStub::class
        );
    }

    /**
     * @return ExceptionHandlerInterface
     */
    protected function createExceptionHandler()
    {
        return $this->getMock(ExceptionHandlerInterface::class);
    }

    public function testDefaultAndSpecialAlias()
    {
        $mockServer1 = $this->getMock(ServerInterface::class);
        $mockServer2 = $this->getMock(ServerInterface::class);

        $this->serverRegistry->add('foo1', $mockServer1, false);
        $this->serverRegistry->add('foo2', $mockServer2, true);

        $mockRequestHandler1 = $this->getMock(RequestHandlerInterface::class);
        $mockRequestHandler2 = $this->getMock(RequestHandlerInterface::class);

        $this->requestHandlerRegistry->add('bar1', $mockRequestHandler1, false);
        $this->requestHandlerRegistry->add('bar2', $mockRequestHandler2, true);

        $exceptionHandler = $this->createExceptionHandler();
        $exe = $this->createFactory()->create(ExecutorFactory::USE_DEFAULT_KEY, ExecutorFactory::USE_DEFAULT_KEY, $exceptionHandler);

        /* @var ExecutorFakeStub $exe */
        $this->assertInstanceOf(ExecutorFakeStub::class, $exe);
        $this->assertSame($mockServer2, $exe->getServer());
        $this->assertSame($mockRequestHandler2, $exe->getRequestHandler());
        $this->assertSame($exceptionHandler, $exe->getExceptionHandler());

        $exe = $this->createFactory()->create('foo1', 'bar1', $exceptionHandler);

        /* @var ExecutorFakeStub $exe */
        $this->assertInstanceOf(ExecutorFakeStub::class, $exe);
        $this->assertSame($mockServer1, $exe->getServer());
        $this->assertSame($mockRequestHandler1, $exe->getRequestHandler());
        $this->assertSame($exceptionHandler, $exe->getExceptionHandler());
    }

    public function testResolveFromContainer()
    {
        $mockServer1 = $this->getMock(ServerInterface::class);
        $mockRequestHandler1 = $this->getMock(RequestHandlerInterface::class);

        $this->container->set('service.foo1', $mockServer1);
        $this->container->set('service.bar1', $mockRequestHandler1);

        $exceptionHandler = $this->createExceptionHandler();
        $exe = $this->createFactory()->create('service.foo1', 'service.bar1', $exceptionHandler);

        /* @var ExecutorFakeStub $exe */
        $this->assertInstanceOf(ExecutorFakeStub::class, $exe);
        $this->assertSame($mockServer1, $exe->getServer());
        $this->assertSame($mockRequestHandler1, $exe->getRequestHandler());
        $this->assertSame($exceptionHandler, $exe->getExceptionHandler());
    }
}
