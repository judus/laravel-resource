<?php

namespace Maduser\Laravel\Resource\Fields;

use Exception;

class Primary extends AbstractField
{
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);
        $this->setInputType('hidden');
        $this->setDbFieldType('int');
    }

    /**
     * Filter the resource by this field and given string
     *
     * Note: Relationship fields are supposed to override this method
     *
     * @param  string  $value
     *
     * @return AbstractField
     * @throws Exception
     */
    public function filter(string $value)
    {
        $this->filtered = $value;

        $this->resource->getQuery()->where($this->getName(), '=', $value);

        return $this;
    }
}
