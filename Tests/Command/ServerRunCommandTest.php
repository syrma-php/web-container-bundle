<?php

namespace Syrma\WebContainerBundle\Tests\Command;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Container;
use Syrma\WebContainer\ExceptionHandlerInterface;
use Syrma\WebContainer\Executor;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainer\Tests\Server\ServerStub;
use Syrma\WebContainerBundle\Command\ServerRunCommand;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddExecutorPass;
use Syrma\WebContainerBundle\Executor\ExecutorRegistry;

class ServerRunCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExecutorRegistry
     */
    protected $executorRegistry;

    protected function setUp()
    {
        parent::setUp();
        $this->executorRegistry = new ExecutorRegistry();
    }

    protected function getContainerServices()
    {
        return array(
            AddExecutorPass::REGISTRY_ID => $this->executorRegistry,
        );
    }

    protected function createContainer()
    {
        $container = new Container();

        foreach ($this->getContainerServices() as $id => $service) {
            $container->set($id, $service);
        }

        return $container;
    }

    protected function createCommand()
    {
        $cmd = new ServerRunCommand();
        $cmd->setContainer($this->createContainer());

        return $cmd;
    }

    public function testDefaultRun()
    {
        $mockParams = array();
        $mockRequestHandler = $this->getMock(RequestHandlerInterface::class);
        $mockRequestHandler->expects($this->once())
            ->method('handle')
            ->willReturn(42);

        $mockServer = new ServerStub(function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) use (&$mockParams) {
            $mockParams = array(
                'address' => $context->getListenAddress(),
                'port' => $context->getListenPort(),
                'handlerId' => $requestHandler->handle($this->getMock(RequestInterface::class)),
            );
        });

        $executor = new Executor($mockServer, $mockRequestHandler, $this->getMock(ExceptionHandlerInterface::class));
        $this->executorRegistry->add('fooBar', $executor, true);

        $cmd = $this->createCommand();
        $cmd->run(new ArrayInput(array()), new NullOutput());

        $this->assertEquals(array(
            'address' => ServerContextInterface::DEFAULT_ADDRESS,
            'port' => ServerContextInterface::DEFAULT_PORT,
            'handlerId' => 42,
        ), $mockParams);
    }

    public function testRun()
    {
        $mockParams = array();
        $mockRequestHandler = $this->getMock(RequestHandlerInterface::class);
        $mockRequestHandler->expects($this->once())
            ->method('handle')
            ->willReturn(42);

        $mockServer = new ServerStub(function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) use (&$mockParams) {
            $mockParams = array(
                'address' => $context->getListenAddress(),
                'port' => $context->getListenPort(),
                'handlerId' => $requestHandler->handle($this->getMock(RequestInterface::class)),
            );
        });

        $executor = new Executor($mockServer, $mockRequestHandler, $this->getMock(ExceptionHandlerInterface::class));
        $this->executorRegistry->add('fooBar', $executor, false);

        $cmd = $this->createCommand();
        $cmd->run(new ArrayInput(array(
            '--listenAddress' => '1.1.1.1',
            '--listenPort' => 8080,
            '--executor' => 'fooBar',
        )), new NullOutput());

        $this->assertEquals(array(
            'address' => '1.1.1.1',
            'port' => 8080,
            'handlerId' => 42,
        ), $mockParams);
    }
}
