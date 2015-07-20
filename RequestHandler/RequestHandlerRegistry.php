<?php

namespace Syrma\WebContainerBundle\RequestHandler;

use Syrma\WebContainer\RequestHandlerInterface;
use Syrma\WebContainerBundle\Util\AbstractRegistry;

/**
 * Registry of avaiable request handlers.
 */
class RequestHandlerRegistry extends AbstractRegistry
{
    /**
     * @param string                  $alias
     * @param RequestHandlerInterface $server
     * @param bool                    $isDefault
     */
    public function add($alias, RequestHandlerInterface $server, $isDefault = false)
    {
        $this->doAdd($alias, $server, $isDefault);
    }

    /**
     * @param string $alias
     *
     * @return RequestHandlerInterface
     */
    public function get($alias)
    {
        return $this->doGet($alias);
    }

    /**
     * @return RequestHandlerInterface
     */
    public function getDefault()
    {
        return $this->get($this->doGetDefault());
    }
}
