<?php

namespace Maduser\Laravel\Resource\Fields;

class DateTime extends Text
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('text');
        $this->setDbFieldType('DATETIME');
    }
}
