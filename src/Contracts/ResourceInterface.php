<?php
namespace Maduser\Laravel\Resource\Contracts;

use Exception;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\MessageBag;
use Maduser\Laravel\Resource\Fields\AbstractField;
use ReflectionException;

/**
 * Interface ResourceInterface
 *
 * @package Maduser\Laravel\Resource\Resource
 */
interface ResourceInterface
{
    /**
     * Resource constructor.
     *
     * @param Model|null $model
     * @param array      $properties
     *
     * @throws Exception
     */
    public function __construct(Model $model = null, array $properties = []);

    /**
     * Populates this class properties with the values of the given array
     *
     * @param array $properties
     */
    public function initializeProperties(array $properties);

    /**
     * Define a unique name for the resource
     *
     * @param string|null $name
     *
     * @return string
     * @throws ReflectionException
     */
    public static function name(string $name = null): string;

    /**
     * Define a title for forms, tables etc. in the frontend
     *
     * @param string|null $title
     *
     * @return string
     * @throws ReflectionException
     */
    public static function title(string $title = null): string;

    /**
     * Define a label for forms, tables etc. in the frontend
     *
     * @param string|null $label
     *
     * @return string
     * @throws ReflectionException
     */
    public static function label(string $label = null): string;

    /**
     * Gets the instance of the Eloquent model this
     * resource uses
     *
     * @return Model
     * @throws Exception
     */
    public function getModel(): Model;

    /**
     * Sets the instance of Eloquent model this
     * resource uses
     *
     * @param mixed $model
     *
     * @return ResourceInterface
     */
    public function setModel(Model $model): ResourceInterface;

    /**
     * Sets the name of the resource
     *
     * @param string $name
     *
     * @return ResourceInterface
     */
    public function setName(string $name): ResourceInterface;

    /**
     * Sets the label of the resource
     *
     * @param string $label
     *
     * @return ResourceInterface
     */
    public function setLabel(string $label): ResourceInterface;

    /**
     * Gets the title of the resource
     *
     * @return string
     * @throws ReflectionException
     */
    public function getTitle(): string;

    /**
     * Gets the label of the resource
     *
     * @return string
     * @throws ReflectionException
     */
    public function getLabel(): string;

    /**
     * Gets the name of the resource
     *
     * @return string
     * @throws ReflectionException
     */
    public function getName(): string;

    /**
     * Sets the title of the resource
     *
     * @param string $title
     *
     * @return ResourceInterface
     */
    public function setTitle(string $title): ResourceInterface;

    /**
     * Gets the field name for related resources to use as preview
     *
     * @return string
     */
    public function getLabelField(): string;

    /**
     * Sets the field name for related resources to use as preview
     *
     * @param string $name
     *
     * @return ResourceInterface
     */
    public function setLabelField(string $name): ResourceInterface;

    /**
     * @param  string  $suffix
     *
     * @return string
     */
    public function getNamespace(string $suffix = '');

    /**
     * Creates fields and set their values if a resource is loaded
     *
     * @param Model|null $model
     *
     * @throws Exception
     */
    public function initializeFields(Model $model = null);

    /**
     * Define initially the array of Field objects
     *
     * @return array
     * @throws Exception
     */
    public function fields(): array;

    /**
     * @param Model $model
     *
     * @return ResourceInterface
     */
    public function setFieldValues(Model $model): ResourceInterface;

    /**
     * Get a collection of fields defined for this resource
     *
     * @return SupportCollection
     */
    public function getFields(): SupportCollection;

    /**
     * Sets the array of Field objects
     *
     * @param array $fields
     *
     * @return ResourceInterface
     * @throws ReflectionException
     */
    public function setFields(array $fields = []): ResourceInterface;

    /**
     * Filters a collection of Field object by their availability
     * in the given context
     *
     * @param SupportCollection $fields
     *
     * @param array             $contexts
     *
     * @return SupportCollection
     */
    public function filterByContexts(SupportCollection $fields, array $contexts = []): SupportCollection;

    /**
     * Gets a Field by its name
     *
     * @param $name
     *
     * @return AbstractField|null
     * @throws Exception
     */
    public function getField($name): AbstractField;

    /**
     * Get the current set contexts
     *
     * @return mixed
     */
    public function getContexts();

    /**
     * Sets contexts which is used when retrieving the fields by contexts
     *
     * @param mixed $contexts
     *
     * @return ResourceInterface
     */
    public function setContexts(array $contexts);

    /**
     * Shortcut method to set contexts
     *
     * @param $contexts
     *
     * @return ResourceInterface
     */
    public function context($contexts): ResourceInterface;

    /**
     * Gets the paginator
     *
     * @return LengthAwarePaginator
     */
    public function getPaginator(): ?LengthAwarePaginator;

    /**
     * Sets the paginator
     *
     * @param  mixed  $paginator
     *
     * @return ResourceInterface
     */
    public function setPaginator($paginator): ResourceInterface;

    /**
     * Forward all undefined method calls to the eloquent model
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments);

    /**
     * Forward all of undefined property calls to the eloquent model
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name);

    /**
     * Gets a collection of field labels
     *
     * @return SupportCollection
     */
    public function getFieldLabels(): SupportCollection;

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
    public function setValues(array $values): ResourceInterface;

    /**
     * Test if a field exists
     *
     * @param $name
     *
     * @return AbstractField|null
     */
    public function hasField($name);

    /**
     * Gets array of the values each field holds
     *
     * @param string|null $name
     *
     * @return array
     */
    public function getValues(string $name = null): array;

    /**
     * Sets the input values for each given field name
     * ['firstname' => 'John', 'lastname' => 'Doe']
     *
     * @param array $inputs
     *
     * @return ResourceInterface
     */
    public function setInputs(array $inputs): ResourceInterface;

    /**
     * Formulates the $this->model->select() with the appropriate fields
     *
     * @return mixed
     */
    public function selectFields();

    /**
     * Gets array of fields which should be used in the $this->model->select().
     * All the pivot table fields are excluded.
     *
     * @return array
     */
    public function getSelectableFields();

    /**
     * Retrieves the id the eloquent model is holding
     * It's a shortcut for $this->model->{$this->getIdentifier()}
     *
     * @return mixed|null
     */
    public function getId();

    /**
     * Define the primary key/field of this resource. This
     * matches the name of the primary/autoincrement column
     * of the DB table the model refers to. Foreign resources
     * need this for the join queries
     *
     * @return mixed
     */
    public function getIdentifier(): string;

    /**
     * Sets the name of the primary key in the table
     *
     * @param string $name
     *
     * @return ResourceInterface
     */
    public function setIdentifier(string $name): ResourceInterface;

    /**
     * Test if this resource has an id
     *
     * @return boolean
     */
    public function exists();

    /**
     * Execute the get database query
     *
     * @return SupportCollection
     */
    public function get();

    /**
     * Turns a collection of eloquent models into a collection
     * of resources
     *
     * @param SupportCollection $items
     *
     * @return SupportCollection
     */
    public function makeCollection(SupportCollection $items);

    /**
     * Execute the paginate database query
     *
     * @param int $perPage
     *
     * @return SupportCollection
     */
    public function paginate(int $perPage = 20);

    /**
     * Limits the query results be the given amount
     *
     * @param int $amount
     *
     * @return ResourceInterface
     */
    public function limit(int $amount): ResourceInterface;

    /**
     * Offsets the query results be the given amount
     *
     * @param int $amount
     *
     * @return ResourceInterface
     */
    public function offset(int $amount): ResourceInterface;

    /**
     * Save the current resource's changed fields to database
     * and synchronize the relationships.
     *
     * @throws Exception
     */
    public function save();

    /**
     * Get the fields which have changed
     *
     * @return SupportCollection
     */
    public function getChangedFields();

    /**
     * Gets an array of two collections for the local fields
     * and the pivot fields
     *
     * @param SupportCollection $fields
     *
     * @return array
     */
    public function cullFields(SupportCollection $fields);

    /**
     * @param SupportCollection $fields
     *
     * @return SupportCollection
     */
    public function getLocalFields(SupportCollection $fields);

    /**
     * Get all the fields which are stored in a pivot table
     *
     * @param SupportCollection $fields
     *
     * @return SupportCollection
     */
    public function getPivotFields(SupportCollection $fields);

    /**
     * Save the local fields input values
     *
     * @param SupportCollection $fields
     *
     * @throws Exception
     */
    public function saveLocalFields(SupportCollection $fields);

    /**
     * Find a resource by it's id
     *
     * @param int $id
     *
     * @return ResourceInterface
     * @throws Exception
     */
    public function find(int $id): ResourceInterface;

    /**
     * Find several resources an array of ids
     *
     * @param array $ids
     *
     * @return SupportCollection
     */
    public function findMany(array $ids): SupportCollection;

    /**
     * Creates and returns a new resource instance
     *
     * @return ResourceInterface
     * @throws Exception
     */
    public static function make(): ResourceInterface;

    /**
     * Gets an array of the relevant properties of this resource
     * to create a new instance
     *
     * @return array
     */
    public function getProperties();

    /**
     * Synchronize the n:n relationships. This removes and adds
     * entries in the pivot tables
     *
     * @param SupportCollection $foreignFields
     */
    public function syncRelationships(SupportCollection $foreignFields);

    /**
     * Validate each field inputs
     *
     * @return ValidatorInterface
     */
    public function validate(): ValidatorInterface;

    /**
     * Get the field inputs
     *
     * @param string|null $name
     *
     * @return array
     */
    public function getInputs(string $name = null): array;

    /**
     * Get an array of validation rules for each field
     *
     * @return array
     */
    public function getValidationRules();

    /**
     * Set validation errors from the given MessageBag
     * on each field
     *
     * @param MessageBag $errors
     */
    public function setFieldErrors(MessageBag $errors);

    /**
     * Returns true if any field in the current resource
     * is filterable
     *
     * @return bool
     */
    public function hasFilterableFields();

    /**
     * Returns true if any field in the current resource
     * is sortable
     *
     * @return bool
     */
    public function hasSortableFields();

    /**
     * Returns true if any field in the current resource
     * is is a BelongsToMany class.
     *
     * @return bool
     */
    public function hasBelongsToManyFields();

    /**
     * Gets all the fields of type BelongsToMany
     *
     * @return SupportCollection
     */
    public function getBelongsToManyFields(): SupportCollection;

    /**
     * Convert the object instance to an array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Convert the object instance to JSON.
     *
     * @param int $options
     *
     * @return string
     *
     * @throws Exception
     */
    public function toJson($options = 0);

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize();

    /**
     * Define initially the Eloquent model class name
     * this resource uses
     *
     * @return string
     */
    public static function model(): string;

    /**
     * @return string
     * @throws ReflectionException
     */
    public static function classBasename();

    /**
     * @return string
     * @throws ReflectionException
     */
    public static function classBasenameSnaked();

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getClassBasename();

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getClassBasenameSnaked();

    /**
     * Returns a list of columns in this Eloquent model's table
     *
     * @param string|null $table
     *
     * @return mixed
     */
    public function getTableColumns(string $table = null): array;
}
