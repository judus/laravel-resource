<?php

namespace Maduser\Laravel\Resource\Fields;

use Closure;

class Select extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('select');
        $this->setDbFieldType('VARCHAR(255)');
    }

    public function getOptions()
    {
        // A closure is already defined, use it
        if (isset($this->definition['options'])
            && $this->definition['options'] instanceof Closure
        ) {
            return $this->getDefinition('options', func_get_args());
        }

        // A closure is not defined, but attributes (textarea from db) is
        if (isset($this->definition['attributes'])) {

            $options = [];

            // Explode the lines, each one is a value/text pair
            $attributes = explode(PHP_EOL, $this->definition['attributes']);

            // Explode the first ":", we want [$value => $text]
            foreach ($attributes as $attribute) {
                $e = explode(':', trim($attribute), 2);
                $options[$e[0]] = isset($e[1]) ? $e[1] : $e[0];
            }

            return $options;
        }

        return [];
    }
}
