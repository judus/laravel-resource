<?php

namespace Maduser\Laravel\Resource\Fields;

class CKEditor extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('ckeditor');
        $this->setDbFieldType('TEXT');
    }
}
