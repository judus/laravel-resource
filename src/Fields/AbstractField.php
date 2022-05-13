<?php

namespace Maduser\Laravel\Resource\Fields;

use ArrayAccess;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use JsonSerializable;
use Maduser\Generic\Traits\ArrayAccessTrait;
use Maduser\Generic\Traits\JsonSerializableTrait;
use Maduser\Generic\Traits\RenderableTrait;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Fields\Plugins\FieldFormatPlugin;
use ReflectionException;
use Stevebauman\Purify\Facades\Purify;

/**
 * Class AbstractField
 *
 * Note: Relationship fields are supposed to override some methods, like
 * getValue(), getOptions(), order(), filter(), etc. as these require advanced
 * queries and processing
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class AbstractField implements ArrayAccess, Htmlable, JsonSerializable
{
    use ArrayAccessTrait;
    use FieldDefinitionTrait;
    use JsonSerializableTrait;
    use RenderableTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var int|null
     */
    protected $cardinality = 1;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var mixed
     */
    protected $input;

    /**
     * @var mixed
     */
    protected $error;

    protected $cssClass = '';

    /**
     * @var bool
     */
    protected $hasInput = false;

    /**
     * @var bool
     */
    protected $hasChanged = false;

    /**
     * @var string
     */
    protected $convertValue;

    /**
     * TODO: This was meant as a helper and I am not sure if this still
     * required or in use. It should hold the currently set contexts in the
     * resource and help the filtering by contexts.
     *
     * This type declaration is very probably wrong, as plural usually means
     * array
     *
     * @var string //
     */
    protected $contexts;

    /**
     * Set this to TRUE if the does not exist in the db-table
     * Do this manually because performance.
     *
     * @var string
     */
    protected $isVirtual = false;

    /**
     * Holds the order direction, when the resource is ordered by this field
     *
     * @var string
     */
    protected $ordered = '';

    /**
     * Holds the filter value, when the resource is filtered by this field
     *
     * @var string
     */
    protected $filtered = '';

    /**
     * Return this field type name
     *
     * @return string
     * @throws ReflectionException
     */
    public function getType(): string
    {
        if (is_null($this->type)) {
            $this->type = $this->getClassBasenameSnaked();
        }

        return $this->type;
    }

    /**
     * Use this only if you are very sure, you want to do something very strange
     *
     * @param string $type
     *
     * @return AbstractField
     */
    public function setType(string $type): AbstractField
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCardinality(): ?int
    {
        return $this->cardinality;
    }

    /**
     * @param int|null $cardinality
     *
     * @return AbstractField
     */
    public function setCardinality(?int $cardinality): AbstractField
    {
        $this->cardinality = $cardinality;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    /**
     * @param mixed $resource
     *
     * @return AbstractField
     */
    public function setResource(ResourceInterface &$resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     *
     * @return AbstractField
     */
    public function setNamespace(string $namespace): AbstractField
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return AbstractField
     */
    public function setValue($value): AbstractField
    {
        // Note: just a reminder, might get useful in the future.
        // The $value could be a object that handles null and empty
        // $this->value = FieldValue::create($value);

        $this->value = $value;

        return $this;
    }

    /**
     * Get the formatted value
     *
     * @param bool $purify
     *
     * @return mixed
     */
    public function getValue(bool $purify = false)
    {
        $output = $purify ? Purify::clean($this->value) : $this->value;;

        $args = [$output, $this, $this->resource];

        if ($formattedOutput = $this->getDefinition('format', $args)) {
            $output = $formattedOutput;
        }

        return $output;
    }

    /**
     * Gets the real value as is
     *
     * @return mixed
     */
    public function getRaw()
    {
        return $this->value;
    }

    /**
     * Gets the value stored in session or else the raw or formatted value
     *
     * @param bool $format
     *
     * @return mixed
     */
    public function populate(bool $format = false)
    {
        if ($oldInputs = session('inputs_' . $this->getNamespace())) {
            if (isset($oldInputs[$this->getName()])) {
                return $oldInputs[$this->getName()];
            }
        }

        return $format ? $this->getValue(false) : $this->getRaw();
    }

    /**
     * @return mixed
     */
    function getOptions()
    {
        $args = [$this->value, $this->resource];

        if ($result = $this->getDefinition('options', $args)) {
            return $result;
        }

        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return mixed
     */
    public function getInputForDatabase()
    {
        return $this->getInput();
    }

    /**
     * @param mixed $input
     *
     * @return AbstractField
     */
    public function setInput($input)
    {
        $this->hasInput = true;

        $input != $this->getRaw() && $this->hasChanged = true;

        $this->input = $input;

        return $this;
    }

    /**
     * Gets the validation errors of this field
     *
     * @param string|null $key
     *
     * @return mixed|null
     * @throws ReflectionException
     */
    public function getErrors(string $key = null)
    {
        if ($errors = session('errors')) {
            // TODO: We don't use namespaced fields. Yet.
            //return $errors->{$this->getNamespace()}->first($this->getName());
            $namespace = is_null($key) ? $this->resource ? $this->resource->getName() : $key : $key;

            return $errors->{$namespace}->first($this->getName());
        }

        return null;
    }

    /**
     * @param string|null $key
     *
     * @return bool
     * @throws ReflectionException
     */
    public function hasErrors(string $key = null): bool
    {
        return empty($this->getErrors($key)) ? false : true;
    }

    /**
     * @param $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    /**
     * @param string $cssClass
     *
     * @return AbstractField
     */
    public function setCssClass(string $cssClass): AbstractField
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * @param mixed $contexts
     *
     * @return AbstractField
     */
    public function setContexts($contexts)
    {
        $this->contexts = $contexts;

        return $this;
    }

    /**
     * @param bool|null $bool
     *
     * @return bool|AbstractField
     */
    public function isVirtual(bool $bool = null)
    {
        if (!is_null($bool)) {
            $this->isVirtual = $bool;

            return $this;
        }

        return $this->isVirtual;
    }

    /**
     * @return string
     */
    public function getOrdered(): string
    {
        return $this->ordered;
    }

    /**
     * @param string $ordered
     *
     * @return AbstractField
     */
    public function setOrdered(string $ordered): AbstractField
    {
        $this->ordered = $ordered;

        return $this;
    }

    /**
     * Tells if the field has a order direction
     *
     * @return bool
     */
    public function isOrdered(): bool
    {
        return !empty($this->ordered);
    }

    /**
     * @return string
     */
    public function getFiltered(): string
    {
        return $this->filtered;
    }

    /**
     * @param string $filtered
     *
     * @return AbstractField
     */
    public function setFiltered(string $filtered): AbstractField
    {
        $this->filtered = $filtered;

        return $this;
    }

    /**
     * Tells if the field has a filter value
     *
     * @return bool
     */
    public function isFiltered(): bool
    {
        return !empty($this->filtered);
    }

    /**
     * @return bool
     */
    public function hasInput()
    {
        return $this->hasInput;
    }

    /**
     * @param bool|null $value
     *
     * @return bool
     */
    public function hasChanged(bool $value = null)
    {
        is_null($value) || $this->hasChanged = $value;

        return $this->hasChanged;
    }

    /**
     * @param array  $definition
     *
     * @param string $convertValue
     *
     * @return AbstractField
     */
    public static function create(array $definition = [], $convertValue = '')
    {
        $class = get_called_class();

        return new $class($definition, $convertValue);
    }

    /**
     * AbstractField constructor.
     *
     * @param array  $definition
     * @param string $convertValue
     *
     * @throws ReflectionException
     */
    public function __construct(array $definition = [], $convertValue = '')
    {
        $this->definition = $definition;
        $this->convertValue = $convertValue;
        $this->type = $this->getClassBasenameSnaked();

        $this->setFormat(function ($value, $field, $resource) {
            if ($plugin = $this->getPlugin($field)) {
                return $plugin->execute();
            }

            return $value;
        });
    }

    public function getPlugin($field)
    {
        if (isset($this->definition['formatter'])) {
            if ($plugin = $this->definition['formatter']) {
                //if ($plugin instanceof FieldFormatPlugin) {
                return $plugin::create($this->resource, $this, $this->value);
                //}
            }
        }

    }

    /**
     * @param $name
     *
     * @return mixed |null
     * @throws Exception
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->{$method}($name);
        }

        if ($value = call_user_func_array([$this, 'getDefinition'], [$name])) {
            return $value;
        }

        throw new Exception('Property "' . $name . '
            " does not exist on field "' . $this->definition['name'].'".'
        );
    }

    public function getDefinedContexts()
    {
        return array_keys(array_merge_recursive(
            array_flip($this->getInContexts() ?? []),
            array_flip($this->getNotInContexts() ?? []),
            array_flip($this->getDisabledInContexts() ?? []),
            array_flip($this->getHiddenInContexts() ?? [])
        ));
    }

    /**
     * Overrides RenderableTrait
     *
     * @return string
     */
    public function getTemplateFile(): string
    {
        $template = 'field-hidden';

        if ( ! $this->isHiddenInContext($this->contexts)) {
            $template = 'field-' . $this->getInputType();
        }

        return 'fields/' . $template;
    }

    /**
     * Overrides RenderableTrait
     *
     * @return array
     */
    public function getTemplateContexts(): array
    {
        return $this->getDefinedContexts();
    }

    /**
     * Overrides RenderableTrait
     *
     * @return string
     */
    public function getTemplateVariableName(): string
    {
        return 'field';
    }

    /**
     * Overrides JsonSerializableTrait
     *
     * @return array
     */
    public function toArray()
    {
        $properties = get_object_vars($this);
        foreach($properties['definition'] as $definition) {
            $properties['options'] = $this->getOptions();
        }

        //$properties = collect($properties)->keys();
        $properties = collect($properties)->only('type', 'value', 'options', 'definition', 'error')->toArray();

        $properties['definition'] = collect($properties['definition'])->only('label', 'name', 'description', 'inContexts')->toArray();

        return $properties;
    }

    /**
     * Sorts the resource by this field and given direction
     *
     * Note: Relationship fields are supposed to override this method
     *
     * @param  string  $direction
     *
     * @return AbstractField
     * @throws Exception
     */
    public function order(string $direction)
    {
        if ($this->isVirtual()) {
            throw new Exception('Sorry, I can\'t order by virtual fields.');
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            throw new Exception('The order direction can only be "asc" or ' .
                '"desc". Received "'.$direction.'"'
            );
        }

        $this->ordered = $direction;

        $this->resource->getQuery()->orderBy($this->getName(), $direction);

        return $this;
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
        if ($this->isVirtual()) {
            throw new Exception('Sorry, I can\'t filter by virtual fields.');
        }

        $value = strlen($value) > 1 ? '%' . $value . '%' : $value . '%';

        $this->filtered = $value;

        $this->resource->getQuery()->where($this->getName(), 'LIKE', $value);

        return $this;
    }
}
