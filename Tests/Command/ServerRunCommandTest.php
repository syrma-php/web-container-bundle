<?php

namespace Syrma\WebContainerBundle\Tests\Command;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Container;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerContextInterface;
use Syrma\WebContainer\ServerInterface;
use Syrma\WebContainerBundle\Command\ServerRunCommand;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddRequestHandlerPass;
use Syrma\WebContainerBundle\DependencyInjection\Compiler\AddServerPass;
use Syrma\WebContainerBundle\RequestHandler\RequestHandlerRegistry;
use Syrma\WebContainerBundle\Server\ServerRegistry;

class ServerRunCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServerRegistry
     */
    protected $serverRegistry;

    /**
     * @var RequestHandlerRegistry
     */
    protected $requestHandlerRegistry;

    protected function setUp()
    {
        parent::setUp();
        $this->serverRegistry = new ServerRegistry();
        $this->requestHandlerRegistry = new RequestHandlerRegistry();
    }

    protected function getContainerServices()
    {
        return array(
            AddServerPass::REGISTRY_ID => $this->serverRegistry,
            AddRequestHandlerPass::REGISTRY_ID => $this->requestHandlerRegistry,
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
        $this->requestHandlerRegistry->add('bar', $mockRequestHandler, true);

        $mockServer = $this->getMock(ServerInterface::class);
        $mockServer->expects($this->once())
            ->method('start')
            ->willReturnCallback(function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) use (&$mockParams) {
                $mockParams = array(
                    'address' => $context->getListenAddress(),
                    'port' => $context->getListenPort(),
                    'handlerId' => $requestHandler->handle($this->getMock(RequestInterface::class)),
                );
            });

        $this->serverRegistry->add('foo', $mockServer, true);

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
        $this->requestHandlerRegistry->add('bar', $mockRequestHandler, false);

        $mockServer = $this->getMock(ServerInterface::class);
        $mockServer->expects($this->once())
            ->method('start')
            ->willReturnCallback(function (ServerContextInterface $context, RequestHandlerInterface $requestHandler) use (&$mockParams) {
                $mockParams = array(
                    'address' => $context->getListenAddress(),
                    'port' => $context->getListenPort(),
                    'handlerId' => $requestHandler->handle($this->getMock(RequestInterface::class)),
                );
            });
        $this->serverRegistry->add('foo', $mockServer, false);

        $cmd = $this->createCommand();
        $cmd->run(new ArrayInput(array(
            '--listenAddress' => '1.1.1.1',
            '--listenPort' => 8080,
            '--serverAlias' => 'foo',
            '--requestHandlerAlias' => 'bar',
        )), new NullOutput());

        $this->assertEquals(array(
            'address' => '1.1.1.1',
            'port' => 8080,
            'handlerId' => 42,
        ), $mockParams);
    }
}
