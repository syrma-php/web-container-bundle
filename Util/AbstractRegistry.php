<?php

namespace Syrma\WebContainerBundle\Util;

/**
 * Registry abstract implementation.
 */
abstract class AbstractRegistry
{
    const EXT_CODE_NOT_EXISTS = 1;
    const EXT_CODE_EMPTY = 2;
    const EXT_CODE_TOO_MANY = 3;

    /**
     * @var object[]
     */
    private $registry = array();

    /**
     * @var string|null
     */
    private $default;

    /**
     * @param string $alias
     * @param object $item
     * @param bool   $isDefault
     */
    protected function doAdd($alias, $item, $isDefault = false)
    {
        $this->registry[$alias] = $item;

        if (true === $isDefault) {
            $this->default = $alias;
        }
    }

    /**
     * @param string $alias
     *
     * @return object
     */
    protected function doGet($alias)
    {
        if (!isset($this->registry[$alias])) {
            throw new \InvalidArgumentException(sprintf(
                'The alias(%s) not found in the registry! Avaiable aliases: %s',
                $alias,
                implode(', ',  array_keys($this->registry))
            ), self::EXT_CODE_NOT_EXISTS);
        }

        return $this->registry[$alias];
    }

    /**
     * @return string
     */
    protected function doGetDefault()
    {
        if (empty($this->registry)) {
            throw new \RuntimeException(
                sprintf('The registry(%s) is empty!', get_class($this)),
                self::EXT_CODE_EMPTY
            );
        }

        if (empty($this->default)) {
            if (1 == count($this->registry)) {
                $default = key($this->registry);
            } else {
                throw new \RuntimeException(
                    'The default alias is empty and to many alias found! Please set the default!',
                    self::EXT_CODE_TOO_MANY
                );
            }
        } else {
            $default = $this->default;
        }

        return $default;
    }
}
