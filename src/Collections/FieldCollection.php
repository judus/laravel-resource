<?php

namespace Maduser\Laravel\Resource\Collections;

use Maduser\Laravel\Resource\Fields\AbstractField;
use Maduser\Laravel\Support\Collections\TypedCollection;
use Maduser\Laravel\ViewModel\RenderableCollection;

class FieldCollection extends RenderableCollection
{
    /**
     * @var array
     */
    protected static $allowedTypes = [AbstractField::class];

}
