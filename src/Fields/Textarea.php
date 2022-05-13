<?php

namespace Maduser\Laravel\Resource\Fields;

class Textarea extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('textarea');
        $this->setDbFieldType('TEXT');
    }
}
