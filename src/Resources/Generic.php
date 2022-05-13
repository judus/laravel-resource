<?php

namespace Maduser\Laravel\Resource\Resources;

use Illuminate\Database\Eloquent\Model;
use Maduser\Laravel\Resource\AbstractResource;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Traits\ObservableResourceTrait;

class Generic extends AbstractResource
{
    /**
     * @inheritDoc
     */
    public static function model(): string
    {
        // TODO: Implement model() method.
    }

    /**
     * Creates and returns a new resource instance
     *
     * @param Model|null $model
     * @param array      $properties
     *
     * @return ResourceInterface
     * @throws Exception
     */
    public static function make(
        Model $model = null,
        array $properties = []
    ): ResourceInterface {

        return new static($model, $properties);
    }

    /**
     * Resource constructor.
     *
     * @param Model|null $model
     * @param array      $properties
     *
     * @throws Exception
     */
    public function __construct(
        Model $model = null,
        array $properties = []
    ) {
//        $model || $model = !empty(static::model()) ? app()->make(static::model());

//        $this->setModel($model);
        //$this->setFields($this->fields());
        //$this->setFieldValues($model);

        $this->initializeProperties($properties);
    }
}
