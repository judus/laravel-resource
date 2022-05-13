<?php

namespace Maduser\Laravel\Resource\Traits;

use Maduser\Laravel\Resource\ResourceObserver;

/**
 * Trait ObservableResourceTrait
 *
 * @package Maduser\Laravel\Resource\Traits
 */
trait ObservableResourceTrait
{
    /**
     * Array of observers for the resource model to use
     *
     * @return array
     */
    public static function observers(): array
    {
        return [
            ResourceObserver::class
        ];
    }
}
