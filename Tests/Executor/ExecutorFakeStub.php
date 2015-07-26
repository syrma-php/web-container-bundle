<?php

namespace Syrma\WebContainerBundle\Tests\Executor;

use Syrma\WebContainer\ExceptionHandlerInterface;
use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainer\ServerInterface;

class ExecutorFakeStub
{
    /**
     * @var ExceptionHandlerInterface
     */
    private $exceptionHandler;
    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;
    /**
     * @var ServerInterface
     */
    private $server;

    /**
     * @param ServerInterface           $server
     * @param RequestHandlerInterface   $requestHandler
     * @param ExceptionHandlerInterface $exceptionHandler
     */
    public function __construct(ServerInterface $server, RequestHandlerInterface $requestHandler, ExceptionHandlerInterface $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->requestHandler = $requestHandler;
        $this->server = $server;
    }

    /**
     * @return ExceptionHandlerInterface
     */
    public function getExceptionHandler()
    {
        return $this->exceptionHandler;
    }

    /**
     * @return RequestHandlerInterface
     */
    public function getRequestHandler()
    {
        return $this->requestHandler;
    }

    /**
     * @return ServerInterface
     */
    public function getServer()
    {
        return $this->server;
    }
}
