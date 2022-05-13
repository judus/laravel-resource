<?php

namespace Maduser\Laravel\Resource\Fields;

class Text extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('text');
        $this->setDbFieldType('VARCHAR(255)');
    }
}
