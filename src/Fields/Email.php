<?php

namespace Maduser\Laravel\Resource\Fields;

class Email extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('email');
        $this->setDbFieldType('VARCHAR(255)');
    }
}
