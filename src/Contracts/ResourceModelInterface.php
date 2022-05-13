<?php

namespace Maduser\Laravel\Resource\Contracts;

interface ResourceModelInterface
{
    /**
     * Return a appropriate ResourceInterface class name that can be used with this model
     *
     * @return string
     */
    public function getResourceClass(): string;

    /**
     * Return a the associated Resource Instance
     *
     * @return ResourceInterface
     */
    public function getResource(): ResourceInterface;
}
