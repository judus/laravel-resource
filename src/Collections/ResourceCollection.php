<?php

namespace Maduser\Laravel\Resource\Collections;

use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Support\Collections\TypedCollection;

class ResourceCollection extends TypedCollection
{
    /**
     * @var array
     */
    protected static $allowedTypes = [ResourceInterface::class];
}
