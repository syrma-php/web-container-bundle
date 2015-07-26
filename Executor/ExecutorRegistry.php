<?php

namespace Syrma\WebContainerBundle\Executor;

use Syrma\WebContainer\Executor;
use Syrma\WebContainerBundle\Util\AbstractRegistry;

/**
 * Registry of avaiable executors.
 */
class ExecutorRegistry extends AbstractRegistry
{
    /**
     * @param string   $alias
     * @param Executor $executor
     * @param bool     $isDefault
     */
    public function add($alias, Executor $executor, $isDefault = false)
    {
        $this->doAdd($alias, $executor, $isDefault);
    }

    /**
     * @param string $alias
     *
     * @return Executor
     */
    public function get($alias)
    {
        return $this->doGet($alias);
    }

    /**
     * @return Executor
     */
    public function getDefault()
    {
        return $this->get($this->doGetDefault());
    }
}
