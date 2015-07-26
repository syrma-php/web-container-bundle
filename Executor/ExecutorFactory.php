<?php

namespace Syrma\WebContainerBundle\Executor;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Syrma\WebContainer\ExceptionHandlerInterface;
use Syrma\WebContainer\Executor;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerInterface;
use Syrma\WebContainerBundle\RequestHandler\RequestHandlerRegistry;
use Syrma\WebContainerBundle\Server\ServerRegistry;

/**
 * Factory for Executor.
 */
class ExecutorFactory
{
    const USE_DEFAULT_KEY = 'default';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ServerRegistry
     */
    private $serverRegistry;

    /**
     * @var RequestHandlerRegistry
     */
    private $requestHandlerRegistry;

    /**
     * @var string
     */
    private $executorClassName;

    /**
     * @param ContainerInterface     $container
     * @param ServerRegistry         $serverRegistry
     * @param RequestHandlerRegistry $requestHandlerRegistry
     * @param string                 $executorClassName
     */
    public function __construct(
        ContainerInterface $container,
        ServerRegistry $serverRegistry,
        RequestHandlerRegistry $requestHandlerRegistry,
        $executorClassName
    ) {
        $this->container = $container;
        $this->executorClassName = $executorClassName;
        $this->requestHandlerRegistry = $requestHandlerRegistry;
        $this->serverRegistry = $serverRegistry;
    }

    /**
     * @param string                    $serverAlias
     * @param string                    $requestHandlerAlias
     * @param ExceptionHandlerInterface $exceptionHandler
     *
     * @return Executor
     */
    public function create($serverAlias, $requestHandlerAlias, ExceptionHandlerInterface $exceptionHandler)
    {
        $className = $this->executorClassName;

        return new $className(
            $this->guessServer($serverAlias),
            $this->guessRequestHandler($requestHandlerAlias),
            $exceptionHandler
        );
    }

    /**
     * @param string $serverAlias
     *
     * @return ServerInterface
     */
    private function guessServer($serverAlias)
    {
        if (self::USE_DEFAULT_KEY == $serverAlias) {
            return $this->serverRegistry->getDefault();
        } elseif ($this->container->has($serverAlias)) {
            return $this->container->get($serverAlias);
        } else {
            return $this->serverRegistry->get($serverAlias);
        }
    }

    /**
     * @param $requestHandlerAlias
     *
     * @return RequestHandlerInterface
     */
    private function guessRequestHandler($requestHandlerAlias)
    {
        if (self::USE_DEFAULT_KEY == $requestHandlerAlias) {
            return $this->requestHandlerRegistry->getDefault();
        } elseif ($this->container->has($requestHandlerAlias)) {
            return $this->container->get($requestHandlerAlias);
        } else {
            return $this->requestHandlerRegistry->get($requestHandlerAlias);
        }
    }
}
