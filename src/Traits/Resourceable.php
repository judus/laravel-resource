<?php

namespace Maduser\Laravel\Resource\Traits;

use Exception;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;

trait Resourceable
{
    /**
     * Return a appropriate ResourceInterface class name that can be used with this model
     *
     * @return string
     */
    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }

    /**
     * Return a the associated Resource Instance
     *
     * @return ResourceInterface
     * @throws Exception
     */
    public function getResource(): ResourceInterface
    {
        if (empty($this->getResourceClass())) {
            throw new Exception('The model ' . __CLASS__ . ' has no resource class');
        }

        return $this->getResourceClass()::create($this);
    }
}
