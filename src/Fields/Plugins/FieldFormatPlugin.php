<?php

namespace Maduser\Laravel\Resource\Fields\Plugins;

use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Fields\AbstractField;

/**
 * Class FieldFormatPlugin
 *
 * @package Maduser\Laravel\Resource\Fields\Plugins
 */
abstract class FieldFormatPlugin implements FieldPluginInterface
{
    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @var AbstractField
     */
    protected $field;

    /**
     * @var
     */
    protected $value;

    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return static::class;
    }

    /**
     * @param $resource
     * @param $field
     * @param $value
     *
     * @return static
     */
    public static function create($resource, $field, $value)
    {
        return new static($resource, $field, $value);
    }

    /**
     * FieldFormatPlugin constructor.
     *
     * @param ResourceInterface $resource
     * @param AbstractField     $field
     * @param                   $value
     */
    public function __construct(
        ResourceInterface $resource,
        AbstractField $field,
        $value
    ) {
        $this->resource = $resource;
        $this->field = $field;
        $this->value = $value;
    }
}
