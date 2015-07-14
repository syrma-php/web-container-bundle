<?php

namespace Syrma\WebContainerBundle\RequestHandler;

use Syrma\WebContainer\RequestHandlerInterface;

/**
 * REgistry of avaiable request handlers.
 */
class RequestHandlerRegistry
{
    /**
     * @var RequestHandlerInterface[]
     */
    private $registry = array();

    /**
     * @var string|null
     */
    private $default;

    /**
     * @param string                  $alias
     * @param RequestHandlerInterface $server
     * @param bool                    $isDefault
     */
    public function add($alias, RequestHandlerInterface $server, $isDefault = false)
    {
        $this->registry[$alias] = $server;

        if (true === $isDefault) {
            $this->default = $alias;
        }
    }

    /**
     * @param string $alias
     *
     * @return RequestHandlerInterface
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
     * @return RequestHandlerInterface
     */
    public function getDefault()
    {
        if (empty($this->registry)) {
            throw new \RuntimeException(
                'The requestHandler registry is empty!'
            );
        }

        if (empty($this->default)) {
            if (0 == count($this->registry)) {
                $default = key($this->registry);
            } else {
                throw new \RuntimeException(
                    'The default requestHandler alias is empty and to many alias found! Please set the default requestHandler!'
                );
            }
        } else {
            $default = $this->default;
        }

        return $this->get($default);
    }
}
