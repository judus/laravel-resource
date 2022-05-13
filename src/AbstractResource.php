<?php

namespace Maduser\Laravel\Resource;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use Maduser\Generic\Traits\JsonSerializableTrait;
use Maduser\Generic\Traits\SelfAwareClass;
use Maduser\Generic\Traits\SelfAwareEloquent;
use Maduser\Laravel\Resource\Collections\FieldCollection;
use Maduser\Laravel\Resource\Collections\ResourceCollection;
use Maduser\Laravel\Resource\Fields\AbstractField;
use Maduser\Laravel\Resource\Fields\BelongsTo;
use Maduser\Laravel\Resource\Fields\BelongsToMany;
use Maduser\Laravel\Resource\Fields\FieldFactory;
use Maduser\Laravel\Resource\Fields\HasMany;
use Maduser\Laravel\Resource\Fields\Identifier;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Traits\MetaFieldsTrait;
use ReflectionException;

/**
 * Class AbstractResource
 *
 * @package Maduser\Laravel\Resource\Resource
 */
abstract class AbstractResource implements ResourceInterface, JsonSerializable
{
    use ForwardsCalls;
    use JsonSerializableTrait;
    use SelfAwareClass;
    use SelfAwareEloquent;
    use MetaFieldsTrait;

    /**
     * @var string
     */
    protected static $name;

    /**
     * @var string
     */
    protected static $label;

    /**
     * @var string
     */
    protected static $title;

    /**
     * @var Model|Builder
     */
    protected $model;

    /**
     * @var string
     */
    protected $identifier = 'id';

    /**
     * @var string
     */
    protected $labelField = 'id';

    /**
     * @var array
     */
    protected $defaultOrder = ['id' => 'desc'];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $contexts = [];

    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginator;

    /**
     * @var
     */
    protected $currentOrder;

    /**
     * @var
     */
    protected $query;


    /**
     * @var array
     */
    protected static $selectOptions = [];

    /**
     * @param ResourceInterface $caller
     * @param AbstractField     $field
     * @param Closure           $callable
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function getSelectOptions(ResourceInterface $caller, AbstractField $field, Closure $callable)
    {
        $key = $caller->getName() . '.' . $field->getName();

        if (!isset(self::$selectOptions[$key])) {
            self::$selectOptions[$key] = $callable();
        }

        return self::$selectOptions[$key];
    }

    /**
     * Sets the instance of Eloquent model this
     * resource uses
     *
     * @param  mixed  $model
     *
     * @return ResourceInterface
     */
    public function setModel(Model $model): ResourceInterface
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Gets the instance of the Eloquent model this
     * resource uses
     *
     * @return mixed
     * @throws Exception
     */
    public function getModel(): Model
    {
        if (is_null($this->model)) {
            throw new Exception(
                'Property $model is undefined in '.get_class($this)
            );
        }

        return $this->model;
    }

    /**
     * Sets the name of the resource
     *
     * @param  string  $name
     *
     * @return ResourceInterface
     */
    public function setName(string $name): ResourceInterface
    {
        self::$name = $name;

        return $this;
    }

    /**
     * Gets the name of the resource
     *
     * @return string
     * @throws ReflectionException
     */
    public function getName(): string
    {
        if (empty(static::$name)) {
            static::$name = str_replace(
                '_resource', '', $this->getClassBasenameSnaked()
            );
        }

        return static::$name;
    }

    /**
     * Sets the label of the resource
     *
     * @param  string  $label
     *
     * @return ResourceInterface
     */
    public function setLabel(string $label): ResourceInterface
    {
        self::$label = $label;

        return $this;
    }

    /**
     * Gets the label of the resource
     *
     * @return string
     * @throws ReflectionException
     */
    public function getLabel(): string
    {
        if (empty(static::$label)) {
            static::$label = ucwords(str_replace('_', ' ', $this->getName()));
        }

        return static::$label;
    }

    /**
     * Sets the title of the resource
     *
     * @param  string  $title
     *
     * @return ResourceInterface
     */
    public function setTitle(string $title): ResourceInterface
    {
        static::$title = $title;

        return $this;
    }

    /**
     * Gets the title of the resource
     *
     * @return string
     * @throws ReflectionException
     */
    public function getTitle(): string
    {
        if (empty(static::$title)) {
            static::$title = Pluralizer::plural($this->getLabel());
        }

        return static::$title;
    }

    /**
     * Gets the field name for related resources to use as preview
     *
     * @return string
     */
    public function getLabelField(): string
    {
        return $this->labelField;
    }

    /**
     * Sets the field name for related resources to use as preview
     *
     * @param  string  $name
     *
     * @return ResourceInterface
     */
    public function setLabelField(string $name): ResourceInterface
    {
        $this->labelField = $name;

        return $this;
    }

    /**
     * Define the primary key/field of this resource. This
     * matches the name of the primary/autoincrement column
     * of the DB table the model refers to. Foreign resources
     * need this for the join queries
     *
     * @return mixed
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Sets the name of the primary key in the table
     *
     * @param  string  $name
     *
     * @return ResourceInterface
     */
    public function setIdentifier(string $name): ResourceInterface
    {
        $this->identifier = $name;

        return $this;
    }

    /**
     * @param  array  $defaultOrder
     *
     * @return AbstractResource
     */
    public function setDefaultOrder(array $defaultOrder): AbstractResource
    {
        $this->defaultOrder = $defaultOrder;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultOrder(): array
    {
        return $this->defaultOrder;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param  int  $perPage
     *
     * @return AbstractResource
     */
    public function setPerPage(int $perPage): AbstractResource
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * @return Builder
     */
    public function getQuery()
    {
        if (is_null($this->query)) {
            $this->query = $this->model->newQuery();
        }

        $this->eagerLoadRelationships($this->query);

        return $this->query;
    }

    /**
     * @param  Builder  $query
     *
     * @return AbstractResource
     */
    public function setQuery(Builder $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Define a unique name for the resource
     *
     * @param  string|null  $name
     *
     * @return string
     * @throws ReflectionException
     */
    public static function name(string $name = null): string
    {
        is_null($name) || static::$name = $name;

        if (empty(static::$name)) {
            static::$name = str_replace(
                '_resource', '', static::classBasenameSnaked()
            );
        }

        return static::$name;
    }

    /**
     * Define a title for forms, tables etc. in the frontend
     *
     * @param  string|null  $title
     *
     * @return string
     * @throws ReflectionException
     */
    public static function title(string $title = null): string
    {
        is_null($title) || static::$title = $title;

        if (empty(static::$title)) {
            static::$title = Pluralizer::plural(static::label());
        }

        return static::$title;
    }

    /**
     * Define a label for forms, tables etc. in the frontend
     *
     * @param  string|null  $label
     *
     * @return string
     * @throws ReflectionException
     */
    public static function label(string $label = null): string
    {
        is_null($label) || static::$label = $label;

        if (empty(static::$label)) {
            static::$label = static::classBasename();
        }

        return static::$label;
    }

    /**
     * @return array
     */
    public static function observers()
    {
        return [
            ResourceObserver::class
        ];
    }

    /**
     * Creates and returns a new resource instance
     *
     * @param  Model|null  $model
     * @param  array  $properties
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

        $model || $model = app()->make(static::model());

        $this->setModel($model);
        $this->setFields($this->fields());
        $this->setFieldValues($model);

        $this->initializeProperties($properties);
    }

    /**
     * Forward all undefined method calls to the eloquent model
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->model, $method], $arguments);
    }

    /**
     * Forward all of undefined property calls to the eloquent model
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->model->{$name};
    }

    /**
     * Populates this class properties with the values of the given array
     *
     * @param array $properties
     */
    public function initializeProperties(array $properties)
    {
        foreach ($properties as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Creates fields and set their values if a resource is loaded
     *
     * @param  Model|null  $model
     *
     * @throws Exception
     */
    public function initializeFields(Model $model = null)
    {
        $this->setFields($this->fields());
        is_null($model) || $this->setFieldValues($model);
    }

    /**
     * Gets an array of the relevant properties of this resource
     * to create a new instance
     *
     * @return array
     */
    public function getProperties()
    {
        $properties = get_object_vars($this);

        unset($properties['model'], $properties['fields']);

        return $properties;
    }

    /**
     * Turns a collection of eloquent models into a collection
     * of resources
     *
     * @param  SupportCollection  $models
     *
     * @return SupportCollection
     */
    public function makeCollection(SupportCollection $models)
    {
        $collection = ResourceCollection::make();

        $models->each(function ($model) use ($collection) {
            $collection->push(self::make($model, $this->getProperties()));
        });

        return $collection;
    }

    /**
     * @param  string  $suffix
     *
     * @return string
     * @throws ReflectionException
     */
    public function getNamespace(string $suffix = '')
    {
        $name = empty($this->namespace) ? $this->getName() : $this->namespace;

        return $name . $suffix;
    }

    /**
     * Sets the array of Field objects
     *
     * @param array $fields
     *
     * @return ResourceInterface
     * @throws ReflectionException
     */
    public function setFields(array $fields = []): ResourceInterface
    {
        $this->fields = [];

        foreach ($fields as $field) {
            $field->setNamespace($this->getNamespace());
            $field->setResource($this);

            if (Identifier::class == get_class($field)) {
                $this->setIdentifier($field->name);
            }

            $this->fields[$field->getName()] = $field;
        }

        return $this;
    }

    /**
     * Get a collection of fields defined for this resource
     *
     * @return SupportCollection
     */
    public function getFields(): SupportCollection
    {
        $fields = FieldCollection::make($this->fields);

        $fields = $this->filterByContexts($fields, func_get_args());

        return $fields;
    }

    /**
     * Get the current set contexts
     *
     * @return mixed
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * Sets contexts which is used when retrieving the fields by contexts
     *
     * @param  mixed  $contexts
     *
     * @return ResourceInterface
     */
    public function setContexts(array $contexts)
    {
        $this->contexts = $contexts;

        foreach ($this->fields as $field) {
            /** @var AbstractField $field */
            $field->setContexts($contexts);
        }

        return $this;
    }

    /**
     * Gets the paginator
     *
     * @return LengthAwarePaginator|null
     */
    public function getPaginator(): ?LengthAwarePaginator
    {
        return $this->paginator;
    }

    /**
     * Sets the paginator
     *
     * @param  mixed  $paginator
     *
     * @return ResourceInterface
     */
    public function setPaginator($paginator): ResourceInterface
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Define initially the array of Field objects
     *
     * @return array
     * @throws Exception
     */
    public function fields(): array
    {
        return FieldFactory::createFromTableSchema($this);
    }

    /**
     * @param  Model  $model
     *
     * @return ResourceInterface
     */
    public function setFieldValues(Model $model): ResourceInterface
    {
        if ($model->exists) {
            foreach ($this->getFields() as $field) {
                $field->setValue($model->{$field->getName()});
            }
        }

        return $this;
    }

    /**
     * Shortcut method to set contexts
     *
     * @param $contexts
     *
     * @return ResourceInterface
     */
    public function context($contexts): ResourceInterface
    {
        $this->setContexts(
            array_unique(array_merge($this->contexts, func_get_args()))
        );

        return $this;
    }

    /**
     * Filters a collection of Field object by their availability
     * in the given context
     *
     * @param  SupportCollection  $fields
     *
     * @param  array  $contexts
     *
     * @return SupportCollection
     * @todo clean this up
     *
     */
    public function filterByContexts(
        SupportCollection $fields,
        array $contexts = []
    ): SupportCollection {
        if (!count($contexts) && !count($this->contexts)) {
            return $fields;
        }

        return $fields->filter(function (AbstractField $field) use ($contexts) {
            if (count($contexts)) {
                return $field->isInContext($contexts);

            } else {
                if (count($this->contexts)) {
                    return
                        (
                            $field->isInContext($this->contexts) ||
                            $field->isHiddenInContext($this->contexts) ||
                            $field->isDisabledInContext($this->contexts)
                        ) && (
                        !$field->isNotInContext($this->contexts)
                        );

                } else {
                    return !$field->isNotInContext($contexts);

                }
            }
        });
    }

    /**
     * Gets a Field by its name
     *
     * @param $name
     *
     * @return AbstractField|null
     * @throws Exception
     */
    public function getField($name): AbstractField
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        throw new Exception('Unknown field name "' . $name . '"');
    }

    /**
     * Gets a collection of field labels
     *
     * @return SupportCollection
     */
    public function getFieldLabels(): SupportCollection
    {
        $labels = [];
        foreach ($this->getFields() as $field) {
            $labels[$field->getName()] = $field->getLabel();
        }

        return collect($labels);
    }

    /**
     * Sets the input values for each given field name
     * ['firstname' => 'John', 'lastname' => 'Doe']
     *
     * Note: this method won't directly set the 'value' of a field,
     * but it's 'input'
     *
     * @param array $values
     *
     * @return ResourceInterface
     */
    public function setValues(array $values): ResourceInterface
    {
        foreach ($values as $key => $value) {
            if ($field = $this->hasField($key)) {
                $field->setInput($value);
            }
        }

        return $this;
    }

    /**
     * Test if a field exists
     *
     * @param $name
     *
     * @return AbstractField|null
     */
    public function hasField($name)
    {
        if ($fields = $this->getFields()) {
            return isset($fields[$name]) ? $fields[$name] : null;
        }

        return null;
    }

    /**
     * Gets array of the values each field holds
     *
     * @param string|null $name
     *
     * @return array
     */
    public function getValues(string $name = null): array
    {
        $values = [];

        foreach ($this->getFields() as $key => $field) {
            $values[$key] = $field->getValue();
        }

        return $values;
    }

    /**
     * Sets the input values for each given field name
     * ['firstname' => 'John', 'lastname' => 'Doe']
     *
     * @param array $inputs
     *
     * @return ResourceInterface
     */
    public function setInputs(array $inputs): ResourceInterface
    {
        foreach ($inputs as $key => $value) {
            if ($field = $this->hasField($key)) {
                $field->setInput($value);
            }
        }

        return $this;
    }


    /**
     * Get the fields which have changed
     *
     * @return SupportCollection
     */
    public function getChangedFields()
    {
        return $this->getFields()->filter(function (AbstractField $field) {
            return $field->hasChanged();
        });
    }

    /**
     * Gets an array of two collections for the local fields
     * and the pivot fields
     *
     * @param  SupportCollection  $fields
     *
     * @return array
     */
    public function cullFields(SupportCollection $fields)
    {
        return [
            $this->getLocalFields($fields),
            $this->getPivotFields($fields)
        ];
    }

    /**
     * @param  SupportCollection  $fields
     *
     * @return SupportCollection
     */
    public function getLocalFields(SupportCollection $fields)
    {
        return $fields->filter(function ($field) {
            /** @var AbstractField $field */
            if ($field->isVirtual()) {
                return false;
            }
            return !in_array(
                get_class($field), [BelongsToMany::class, HasMany::class]
            );
        });
    }

    /**
     * Get all the fields which are stored in a pivot table
     *
     * @param  SupportCollection  $fields
     *
     * @return SupportCollection
     */
    public function getPivotFields(SupportCollection $fields)
    {
        return $fields->filter(function ($field) {
            /** @var AbstractField $field */
            if ($field->isVirtual()) {
                return false;
            }
            return get_class($field) == BelongsToMany::class;
        });
    }

    /**
     * Formulates the $this->model->select() with the appropriate fields
     *
     * @return mixed
     */
    public function selectFields()
    {
        return call_user_func_array([$this->model, 'select'],
            [implode(', ', $this->getSelectableFields())]);
    }

    /**
     * Gets array of fields which should be used in the $this->model->select().
     * All the pivot table fields are excluded.
     *
     * @return array
     */
    public function getSelectableFields()
    {
        $fields = $this->getFields()->filter(function ($item) {
            return !(get_class($item) == BelongsToMany::class);
        });

        return array_keys($fields->toArray());
    }

    /**
     * Retrieves the id the eloquent model is holding
     * It's a shortcut for $this->model->{$this->getIdentifier()}
     *
     * @return mixed|null
     */
    public function getId()
    {
        return $this->model ? $this->model->{$this->getIdentifier()} : null;
    }

    /**
     * Test if this resource has an id
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->model && $this->model->{$this->getIdentifier()} ?
            true : false;
    }

    /**
     * Find a resource by it's id
     *
     * @param  int  $id
     *
     * @return ResourceInterface
     * @throws Exception
     */
    public function find(int $id): ResourceInterface
    {
        $item = $this->model->find($id);

        return self::make($item, $this->getProperties());
    }

    /**
     * Find multiple resources by an array of ids
     *
     * @param  array  $ids
     *
     * @return SupportCollection
     */
    public function findMany(array $ids): SupportCollection
    {
        $items = $this->model->findMany($ids);

        return $this->makeCollection($items);
    }

    /**
     * Find a resource by it's id
     *
     * @param  int  $id
     *
     * @return ResourceInterface
     * @throws Exception
     */
    public function findOrFail(int $id): ResourceInterface
    {
        $item = $this->model->findOrFail($id);

        return self::make($item, $this->getProperties());
    }

    /**
     * Execute the get database query
     *
     * @return SupportCollection
     */
    public function get()
    {
        $items = $this->getQuery()->get();

        return $this->makeCollection($items);
    }

    /**
     * Execute the paginate database query
     *
     * @param  int  $perPage
     *
     * @return SupportCollection
     * @throws Exception
     */
    public function paginate(int $perPage = null)
    {
        $perPage = is_null($perPage) ? $this->perPage : $perPage;

        if (!$this->hasOrderedFields()) {
            //$this->orderBy($this->getDefaultOrder());
        }

        $this->paginator = $this->getQuery()->paginate($perPage);

        return $this->makeCollection($this->paginator->getCollection());
    }

    /**
     * Limits the query results be the given amount
     *
     * @param int $amount
     *
     * @return ResourceInterface
     */
    public function limit(int $amount): ResourceInterface
    {
        $this->getQuery()->limit($amount);

        return $this;
    }

    /**
     * Offsets the query results be the given amount
     *
     * @param int $amount
     *
     * @return ResourceInterface
     */
    public function offset(int $amount): ResourceInterface
    {
        $this->getQuery()->offset($amount);

        return $this;
    }

    /**
     * Save the current resource's changed fields to database
     * and synchronize the relationships.
     *
     * @throws Exception
     */
    public function save()
    {
        // BelongsToMany fields, e.g. multiple selects fields won't
        // submit anything if the user selects no options. This means
        // if a BelongsToMany field (from the current context) hasn't been
        // submitted, all the relationships should be removed
        // the update/removal of relationships has to be forced.

        // Get the fields that have a changed value
        $fields = $this->getChangedFields();

        // Find all the BelongsToMany which haven't been submitted/changed
        // and add them to $fields
        foreach ($this->getBelongsToManyFields() as $key => $belongsToMany) {
            if (!$fields->has($key)) {
                $belongsToMany->setInput([]);
                $fields->put($key, $belongsToMany);
            }
        }

        // Unchecked checkboxes aren't sent therefore we have to add them manually
        foreach ($this->getCheckboxFields() as $key => $checkbox) {
            if (!$fields->has($key)) {
                //$checkbox->setInput(0);
                $fields->put($key, $checkbox);
            }
        }

        // This should always be greater than 0 when the resource has
        // BelongsToMany fields
        if ($fields->count()) {
            list($localFields, $foreignFields) = $this->cullFields($fields);

            $localFields->count() && $this->saveLocalFields($localFields);
            $foreignFields->count() && $this->syncRelationships($foreignFields);
        }
    }

    /**
     * Save the local fields input values
     *
     * @param SupportCollection $fields
     *
     * @throws Exception
     */
    public function saveLocalFields(SupportCollection $fields)
    {
        // Set the input values on the Eloquent model
        $fields->each(function ($field) {
            /** @var AbstractField $field */
            $this->model->{$field->getName()} = $field->getInputForDatabase();
        });
        // Save the model
        $this->model->save();

        // Refresh the current resource
        $this->find($this->model->{$this->getIdentifier()});
    }

    /**
     * Synchronize the n:n relationships. This removes and adds
     * entries in the pivot tables
     *
     * @param SupportCollection $foreignFields
     */
    public function syncRelationships(SupportCollection $foreignFields)
    {
        $foreignFields->each(function ($field) {
            /** @var AbstractField $field */
            $this->model->{$field->getMethod()}()
                ->sync($field->getInputForDatabase());
        });
    }

    /**
     * Formulate the eager loading of relationships for a query
     *
     * @param Builder $query
     */
    public function eagerLoadRelationships(Builder $query)
    {
        // Get belongsTo and belongsToMany fields
        $fields = $this->getBelongsToFields()
            ->merge($this->getBelongsToManyFields());

        $methods = [];
        foreach ($fields as $field) {
            /** @var AbstractField $field */
            $methods[] = $field->getMethod();
        }

        if (count($methods) > 0) {
            $query->with($methods);
        }
    }

    /**
     * Get the field inputs
     *
     * @param string|null $name
     *
     * @return array
     */
    public function getInputs(string $name = null): array
    {
        $inputs = [];

        foreach ($this->getFields() as $field) {
            /** @var AbstractField $field */
            if ($field->hasInput()) {
                $inputs[$field->getName()] = $field->getInput();
            }
        }

        return $inputs;
    }

    /**
     * Validate each field inputs
     *
     * @return ValidatorInterface
     */
    public function validate(): ValidatorInterface
    {
        $validator = Validator::make(
            $this->getInputs(), $this->getValidationRules()
        );

        $fieldNames = $this->getFieldLabels()->toArray();
        $validator->addCustomAttributes($fieldNames);

        return $validator;
    }

    /**
     * Get an array of validation rules for each field
     *
     * @return array
     */
    public function getValidationRules()
    {
        $rules = [];

        foreach ($this->getFields() as $key => $field) {
            /** @var AbstractField $field */
            if (!empty($field->getRules()) && !$field->isDisabled()) {
                $rules[$key] = $field->getRules();
            }
        }

        return $rules;
    }

    /**
     * Set validation errors from the given MessageBag
     * on each field
     *
     * @param MessageBag $errors
     */
    public function setFieldErrors(MessageBag $errors)
    {
        foreach ($this->getFields() as $key => $field) {
            /** @var AbstractField $field */
            $field->setError(collect($errors->get($key)));
        }
    }

    /**
     * Returns true if any field in the current resource
     * is filterable
     *
     * @return bool
     */
    public function hasFilterableFields()
    {
        foreach ($this->getFields() as $key => $field) {
            /** @var AbstractField $field */
            if ($field->isFilterable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if any field in the current resource
     * is sortable
     *
     * @return bool
     */
    public function hasSortableFields()
    {
        foreach ($this->getFields() as $key => $field) {
            /** @var AbstractField $field */
            if ($field->isSortable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if any field in the current resource
     * is is a BelongsToMany class.
     *
     * @return bool
     */
    public function hasBelongsToManyFields()
    {
        foreach ($this->getFields() as $key => $field) {
            /** @var AbstractField $field */
            if (get_class($field) == BelongsToMany::class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets all the fields of type BelongsTo
     *
     * @return SupportCollection
     */
    public function getBelongsToFields(): SupportCollection
    {
        return $this->getFields()->filter(function ($field) {
            /** @var AbstractField $field */
            return get_class($field) == BelongsTo::class;
        });
    }

    /**
     * Gets all the fields of type BelongsToMany
     *
     * @return SupportCollection
     */
    public function getBelongsToManyFields(): SupportCollection
    {
        return $this->getFields()->filter(function ($field) {
            /** @var AbstractField $field */
            return get_class($field) == BelongsToMany::class;
        });
    }

    /**
     * Gets all the fields of type BelongsToMany
     *
     * @return SupportCollection
     */
    public function getForeignFields(): SupportCollection
    {
        return $this->getFields()->filter(function ($field) {
            /** @var AbstractField $field */
            return in_array(
                get_class($field), [BelongsToMany::class, HasMany::class]
            );
        });
    }

    /**
     * Gets all the fields of type checkbox
     *
     * @return SupportCollection
     */
    public function getCheckboxFields(): SupportCollection
    {
        return $this->getFields()->filter(function ($field) {
            /** @var AbstractField $field */
            return $field->getInputType() == 'checkbox';
        });
    }

    /**
     * Order the query by the given field/direction pairs
     *
     * Example: ['name' => 'asc', 'id' => 'asc']
     *
     * @param  array  $orders
     *
     * @throws Exception
     */
    public function orderBy(array $orders = [])
    {
        foreach ($orders as $field => $direction) {
            /** @var AbstractField $field */
            if ($field = $this->getField($field)) {
                $field->order($direction);
            }
        }
    }

    /**
     *
     */
    public function hasOrderedFields()
    {
        foreach ($this->getFields() as $field) {
            /** @var AbstractField $field */
            if (!empty($field->getOrdered())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filters the query by the given field/value pairs
     *
     * Example: ['firstname' => 'john', 'lastname' => 'doe']
     *
     * @param  array  $filters
     *
     * @throws Exception
     */
    public function filterBy(array $filters = [])
    {
        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                /** @var AbstractField $field */
                if ($field = $this->getField($field)) {
                    $field->filter($value);
                }
            }
        }
    }

}
