<?php

namespace Maduser\Laravel\Resource\Fields;

/**
 * Class Field
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class Integer extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('number');
        $this->setDbFieldType('INT(11)');
    }
}
