<?php

namespace Maduser\Laravel\Resource\Fields;

/**
 * Class Field
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class Boolean extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('checkbox');
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return (is_null($this->input) || $this->input == false) ? 0 : 1;
    }
}
