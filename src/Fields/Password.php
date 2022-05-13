<?php

namespace Maduser\Laravel\Resource\Fields;

use Illuminate\Support\Facades\Hash;

class Password extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('password');
        $this->setDbFieldType('VARCHAR(255)');
    }

    public function getInputForDatabase()
    {
        return Hash::make($this->getInput());
    }

}
