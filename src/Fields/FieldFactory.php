<?php

namespace Maduser\Laravel\Resource\Fields;

use Doctrine\DBAL\Schema\Column;
use Exception;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;

/**
 * Class FieldFactory
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class FieldFactory
{
    /**
     * @var array
     */
    protected static $types = [
        'primary' => Primary::class,
        'identifier' => Identifier::class,
        'boolean' => Boolean::class,
        'integer' => Integer::class,
        'number' => Number::class,
        'float' => Number::class,
        'string' => Text::class,
        'text' => Text::class,
        'textarea' => Textarea::class,
        'ckeditor' => CKEditor::class,
        'select' => Select::class,
        'select-multiple' => Select::class,
        'range' => Number::class,
        'datetime' => DateTime::class,
        'date' => Date::class,
        'has-many' => HasMany::class,
        'belongs-to' => BelongsTo::class
    ];

    protected static $typesFromDb = [
        'primary' => Primary::class,
        'identifier' => Identifier::class,
        'boolean' => Boolean::class,
        'integer' => Number::class,
        'number' => Number::class,
        'float' => Number::class,
        'string' => Text::class,
        'text' => Textarea::class,
        'textarea' => Textarea::class,
        'select' => Select::class,
        'select-multiple' => Select::class,
        'range' => Number::class,
        'datetime' => DateTime::class,
        'date' => Date::class,
    ];

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return self::$types;
    }

    /**
     * @param array $types
     */
    public static function setTypes(array $types): void
    {
        self::$types = $types;
    }

    /**
     * @return array
     */
    public static function getTypesFromDb(): array
    {
        return self::$typesFromDb;
    }

    /**
     * @param array $typesFromDb
     */
    public static function setTypesFromDb(array $typesFromDb): void
    {
        self::$typesFromDb = $typesFromDb;
    }


    public static function addType(string $typeName, string $class): void
    {
        self::$types[$typeName] = $class;
    }

    /**
     * @param string     $type
     * @param string     $name
     * @param array      $options
     * @param array|null $fieldTypeMap
     *
     * @return AbstractField
     * @throws Exception
     */
    public static function create(string $type, string $name, array $options = [], array $fieldTypeMap = null): AbstractField
    {
        $fieldTypeMap = $fieldTypeMap ? $fieldTypeMap : static::$types;

        if (isset($fieldTypeMap[$type])) {
            $options['type'] = $type;
            $options['name'] = $name;

            $class = $fieldTypeMap[$type];
            return $class::create($options);
        }

        throw new Exception('Unknown field type "' . $type);
    }

    /**
     * Creates fields based on the database table schema
     * composer require doctrine/dbal
     *
     * @param ResourceInterface $resource
     *
     * @return array
     * @throws Exception
     */
    public static function createFromTableSchema(ResourceInterface $resource)
    {
        $fields = [];

        $columns = $resource->getTableColumns();

        foreach ($columns as $column) {
            /** @var Column $column */

            $field = static::create(static::resolveType($column), $column->getName(), [
                'rules' => implode('|', static::makeRules($column))
            ], static::$typesFromDb);

            if (in_array($field->getName(), [
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'deleted_by'
            ])) {
                $field->addNotInContext('form');
            }

            if (in_array($field->getName(), [
                'deleted_at',
                'deleted_by',
            ])) {
                $field->addNotInContext('table');
            }

            $fields[$column->getName()] = $field;
        }

        return $fields;
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public static function resolveType(Column $column)
    {
        return $column->getAutoincrement() ? 'primary' : $column->getType()->getName();
    }

    /**
     * @param Column $column
     *
     * @return array
     */
    public static function makeRules(Column $column): array
    {
        $rules = [];

        // No rules for the primary field
        if ($column->getAutoincrement()) {
            return [];
        }

        if ($column->getNotnull()) {
            $rules[] = 'required';
        }

        if ($column->getLength() > 0) {
            $rules[] = 'max:' . $column->getLength();
        }

        return $rules;
    }
}
