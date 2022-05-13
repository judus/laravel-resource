<?php

namespace Maduser\Laravel\Resource\Fields;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;

/**
 * Trait FieldDefinitionTrait
 *
 * @package Maduser\Laravel\Resource\Fields
 */
trait FieldDefinitionTrait
{
    /**
     * @var array
     */
    protected $definition;

    /**
     * @var mixed
     */
    protected $foreignResource;

    /**
     * Get the value for a definition key, return NULL if the key does not exist
     * If definition key contains a closure, take the return value of the
     * closure.
     *
     * Returns the definition array when not parameter given
     *
     * @param string|null $name
     * @param array       $params
     *
     * @return mixed
     */
    public function getDefinition(string $name = null, array $params = [])
    {
        if (!$name) {
            return $this->definition;
        }

        if (!isset($this->definition[$name])) {
            return null;
        }

        if ($this->definition[$name] instanceof Closure) {
            return call_user_func_array($this->definition[$name], $params);
        }

        return $this->definition[$name];
    }

    /**
     * @param array $definition
     *
     * @return AbstractField
     */
    public function setDefinition(array $definition): AbstractField
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * @param string $resourceClass
     *
     * @return $this
     */
    public function setForeignResource(string $resourceClass)
    {
        $this->definition['foreignResource'] = $resourceClass;

        return $this;
    }

    /**
     * @return ResourceInterface
     * @throws BindingResolutionException
     */
    public function getForeignResource()
    {
        if ($this->foreignResource) return $this->foreignResource;

        if (! isset($this->getDefinition()['foreignResource'])) return null;

        $this->foreignResource = app()->make($this->getDefinition()['foreignResource']);

        return $this->foreignResource;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->getDefinition('method', func_get_args());
    }

    /**
     * @param mixed $method
     *
     * @return AbstractField
     */
    public function setMethod($method)
    {
        $this->definition['method'] = $method;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getDefinition('name', func_get_args());
    }

    /**
     * @param mixed $name
     *
     * @return AbstractField
     */
    public function setName($name): AbstractField
    {
        $this->definition['name'] = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        if (! $value = $this->getDefinition('label', func_get_args())) {
            $value = ucfirst($this->getDefinition('name', func_get_args()));
        }

        return $value;
    }

    /**
     * @param mixed $label
     *
     * @return AbstractField
     */
    public function setLabel($label): AbstractField
    {
        $this->definition['label'] = $label;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->getDefinition('description', func_get_args());
    }

    /**
     * @param $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->definition['description'] = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDescription()
    {
        return ! empty($this->getDefinition('description', func_get_args()));
    }

    /**
     * @return mixed
     */
    public function getInputType()
    {
        return $this->getDefinition('inputType', func_get_args());
    }

    /**
     * @param mixed $inputType
     *
     * @return AbstractField
     */
    public function setInputType($inputType): AbstractField
    {
        $this->definition['inputType'] = $inputType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->getDefinition('format', func_get_args());
    }

    /**
     * @param mixed $inputType
     *
     * @return AbstractField
     */
    public function setFormat($inputType): AbstractField
    {
        $this->definition['format'] = $inputType;

        return $this;
    }

    /**
     * @param null $value
     *
     * @return mixed
     */
    public function isDisabled($value = null)
    {
        if (is_null($value)) {
            return $this->getDefinition('disabled', func_get_args());
        }

        $this->definition['disabled'] = $value;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return AbstractField
     */
    public function setDisabled($value): AbstractField
    {
        $this->definition['format'] = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlaceholder()
    {
        return $this->getDefinition('placeholder', func_get_args());
    }

    /**
     * @param mixed $placeholder
     *
     * @return AbstractField
     */
    public function setPlaceholder($placeholder): AbstractField
    {
        $this->definition['placeholder'] = $placeholder;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPlaceholder()
    {
        return !empty($this->getDefinition('placeholder', func_get_args()));
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->getDefinition('icon', func_get_args());
    }

    /**
     * @param mixed $icon
     *
     * @return AbstractField
     */
    public function setIcon($icon): AbstractField
    {
        $this->definition['icon'] = $icon;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasIcon()
    {
        return !empty($this->getDefinition('icon', func_get_args()));
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->getDefinition('options', func_get_args());
    }

    /**
     * @param mixed $options
     *
     * @return AbstractField
     */
    public function setOptions($options): AbstractField
    {
        $this->definition['options'] = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->getDefinition('rules', func_get_args());
    }

    /**
     * @param mixed $rules
     *
     * @return AbstractField
     */
    public function setRules($rules): AbstractField
    {
        $this->definition['rules'] = $rules;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbFieldType()
    {
        return $this->getDefinition('dbFieldType', func_get_args());
    }

    /**
     * @param mixed $dbFieldType
     *
     * @return AbstractField
     */
    public function setDbFieldType($dbFieldType)
    {
        $this->definition['dbFieldType'] = $dbFieldType;

        return $this;
    }

    /**
     * @param $definitionName
     *
     * @return mixed
     */
    public function defineAsArray($definitionName)
    {
        if (! isset($this->definition[$definitionName]) || ! is_array($this->definition[$definitionName])) {
            $this->definition[$definitionName] = [];
        }

        return $this->definition[$definitionName];
    }

    /**
     * @param $definitionName
     * @param $value
     *
     * @return bool
     */
    public function isInDefinition($definitionName, array $value)
    {
        $this->defineAsArray($definitionName);

        return array_intersect($value, $this->definition[$definitionName]);
    }

    /**
     * @param $contextName
     * @param $context
     */
    public function addToContext($contextName, $context)
    {
        $this->defineAsArray($contextName);
        $this->definition[$contextName][] = $context;
    }

    /**
     * @return mixed
     */
    public function getInContexts()
    {
        return $this->getDefinition('inContexts', func_get_args());
    }

    /**
     * @param array $contexts
     *
     * @return AbstractField
     */
    public function setInContexts(array $contexts)
    {
        $this->definition['inContexts'] = $contexts;

        return $this;
    }

    /**
     * @param $context
     *
     * @return AbstractField
     */
    public function addInContext($context)
    {
        $this->addToContext('inContexts', $context);

        return $this;
    }

    /**
     * @param $contexts
     *
     * @return bool|null
     */
    public function isInContext($contexts)
    {
        return $this->isInDefinition('inContexts', $contexts);
    }

    /**
     * @return mixed
     */
    public function getNotInContexts()
    {
        return $this->getDefinition('notInContexts', func_get_args());
    }

    /**
     * @param mixed $contexts
     *
     * @return AbstractField
     */
    public function setNotInContexts(array $contexts)
    {
        $this->definition['notInContexts'] = $contexts;

        return $this;
    }

    /**
     * @param $context
     *
     * @return FieldDefinitionTrait
     */
    public function addNotInContext($context)
    {
        $this->addToContext('notInContexts', $context);

        return $this;
    }

    /**
     * @param $contexts
     *
     * @return bool|null
     */
    public function isNotInContext($contexts)
    {
        return $this->isInDefinition('notInContexts', $contexts);
    }

    /**
     * @return mixed
     */
    public function getHiddenInContexts()
    {
        return $this->getDefinition('hiddenInContexts', func_get_args());
    }

    /**
     * @param array $contexts
     *
     * @return AbstractField
     */
    public function setHiddenInContexts(array $contexts)
    {
        $this->definition['hiddenInContexts'] = $contexts;

        return $this;
    }

    /**
     * @param $context
     */
    public function addHiddenInContext($context)
    {
        $this->addToContext('hiddenInContexts', $context);
    }

    /**
     * @param $contexts
     *
     * @return bool
     */
    public function isHiddenInContext($contexts)
    {
        if (is_null($contexts)) return false;

        return $this->isInDefinition('hiddenInContexts', $contexts);
    }

    /**
     * @return mixed
     */
    public function getDisabledInContexts()
    {
        return $this->getDefinition('disabledInContexts', func_get_args());
    }

    /**
     * @param array $contexts
     *
     * @return AbstractField
     */
    public function setDisabledInContexts(array $contexts)
    {
        $this->definition['disabledInContexts'] = $contexts;

        return $this;
    }

    /**
     * @param $context
     */
    public function addDisabledInContext($context)
    {
        $this->addToContext('disabledInContext', $context);
    }

    /**
     * @param $contexts
     *
     * @return bool
     */
    public function isDisabledInContext($contexts)
    {
        if (is_null($contexts)) return false;

        return $this->isInDefinition('disabledInContext', $contexts);
    }



    /**
     * @param  bool|null  $bool
     *
     * @return mixed
     */
    public function isFilterable(bool $bool = null)
    {
        if (is_null($bool)) {
            return $this->getDefinition('filterable');
        }

        $this->definition['filterable'] = $bool;
        return $this;
    }

    /**
     * @param  bool|null  $bool
     *
     * @return mixed
     */
    public function isSortable(bool $bool = null)
    {
        if (is_null($bool)) {
            return $this->getDefinition('sortable');
        }

        $this->definition['sortable'] = $bool;
        return $this;
    }
}
