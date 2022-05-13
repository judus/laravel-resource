<?php

namespace Maduser\Laravel\Resource\Fields;

/**
 * Class FieldValue
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class FieldValue
{
    /**
     * @var
     */
    public $value;

    private $output;

    /**
     * @param        $value
     * @param string $output
     *
     * @return mixed
     */
    public static function create($value, $output = 'JSON')
    {
        $class = get_called_class();
        return new $class($value, $output);
    }

    /**
     * FieldValue constructor.
     *
     * @param        $value
     * @param string $output
     */
    public function __construct($value, $output = 'JSON')
    {
        $this->value = $value;
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (is_null($this->value)) return '';

        if (is_array($this->value)) return implode(', ', $this->value);

        if (is_object($this->value) && method_exists($this->value, 'toArray')) {
            return implode(', ', $this->value->toArray());
        }

        return (string) $this->value;
    }
}
