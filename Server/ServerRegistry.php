<?php

namespace Syrma\WebContainerBundle\Server;

use Syrma\WebContainer\ServerInterface;

/**
 * Registry of avaiable servers.
 */
class ServerRegistry
{
    /**
     * @var ServerInterface[]
     */
    private $registry = array();

    /**
     * @var string|null
     */
    private $default;

    /**
     * @param string          $alias
     * @param ServerInterface $server
     * @param bool            $isDefault
     */
    public function add($alias, ServerInterface $server, $isDefault = false)
    {
        $this->registry[$alias] = $server;

        if (true === $isDefault) {
            $this->default = $alias;
        }
    }

    /**
     * @param string $alias
     *
     * @return ServerInterface
     */
    public function get($alias)
    {
        if (!isset($this->registry[$alias])) {
            throw new \InvalidArgumentException(sprintf(
                'The alias(%s) not found in the registry! Avaiable aliases: %s',
                $alias,
                implode(', ',  array_keys($this->registry))
            ));
        }

        return $this->registry[$alias];
    }

    /**
     * @return ServerInterface
     */
    public function getDefault()
    {
        if (empty($this->registry)) {
            throw new \RuntimeException(
                'The server registry is empty!'
            );
        }

        if (empty($this->default)) {
            if (0 == count($this->registry)) {
                $default = key($this->registry);
            } else {
                throw new \RuntimeException(
                    'The default server alias is empty and to many alias found! Please set the default server!'
                );
            }
        } else {
            $default = $this->default;
        }

        return $this->get($default);
    }
}
