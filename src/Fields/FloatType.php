<?php

namespace Maduser\Laravel\Resource\Fields;

/**
 * Class Field
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class FloatType extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        // TODO: set the correct html input type
        $this->setInputType('number');

        // TODO: set the correct sql column type
        $this->setDbFieldType('FLOAT()');
    }
}
