<?php

namespace Maduser\Laravel\Resource\Fields;

class TextareaHtml extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('textareahtml');
        $this->setDbFieldType('TEXT');
    }
}
