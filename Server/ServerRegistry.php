<?php

namespace Syrma\WebContainerBundle\Server;

use Syrma\WebContainer\ServerInterface;
use Syrma\WebContainerBundle\Util\AbstractRegistry;

/**
 * Registry of avaiable servers.
 */
class ServerRegistry extends AbstractRegistry
{
    /**
     * @param string          $alias
     * @param ServerInterface $server
     * @param bool            $isDefault
     */
    public function add($alias, ServerInterface $server, $isDefault = false)
    {
        $this->doAdd($alias, $server, $isDefault);
    }

    /**
     * @param string $alias
     *
     * @return ServerInterface
     */
    public function get($alias)
    {
        return $this->doGet($alias);
    }

    /**
     * @return ServerInterface
     */
    public function getDefault()
    {
        return $this->get( $this->doGetDefault() );
    }
}
