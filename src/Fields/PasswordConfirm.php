<?php

namespace Maduser\Laravel\Resource\Fields;

class PasswordConfirm extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('password-confirm');
        $this->setDbFieldType('VARCHAR(255)');
    }
}

