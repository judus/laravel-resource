<?php

namespace Maduser\Laravel\Resource\Fields;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\HtmlString;
use ReflectionException;

/**
 * Class BelongsTo
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class BelongsTo extends AbstractField
{
    /**
     * BelongsTo constructor.
     *
     * @param array $definition
     *
     * @throws ReflectionException
     */
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);

        $this->setInputType('select');
        $this->setDbFieldType('VARCHAR(255)');

        $this->setFormat(function($value, $field, $resource) {
            $url = route('resource.show', [$field->getForeignResource()->getName(), $value]);
            $text = $field->resolve($resource, $value);
            return new HtmlString('<a href="'.$url.'">'.$text.'</a>');
        });

    }

    /**
     * @param $closure
     *
     * @return $this
     */
    public function formatOptions($closure)
    {
        $this->definition['formatOptions'] = $closure;

        return $this;
    }

    /**
     * @return array|mixed
     * @throws BindingResolutionException
     * @throws BindingResolutionException
     * @throws BindingResolutionException
     */
    public function getOptions()
    {
        $options = $this->getDefinition('options', [$this]);

        if (!is_null($options)) {
            return $options;
        }

        $items = $this->getForeignModels();
        //$items = [];

        if ($options = $this->getDefinition('formatOptions', [$items, $this])) {
            return $options;
        }

        return $this->getFormattedOptions($items);
    }

    /**
     * @return mixed
     * @throws BindingResolutionException
     */
    public function getForeignModels()
    {
        $foreignResource = $this->getForeignResource();

        return $foreignResource->getSelectOptions($this->getResource(), $this, function() use ($foreignResource) {
            $model = $foreignResource->getModel();
            $primary = $foreignResource->getIdentifier();
            $label = $foreignResource->getLabelField();

            //return $model->select($primary, $label)->orderBy($label)->get();
            return $model->orderBy($label)->get();
        });

    }

    /**
     * @param $items
     *
     * @return array
     * @throws BindingResolutionException
     */
    public function getFormattedOptions($items)
    {
        $options = [];
        $foreignResource = $this->getForeignResource();
        $primary = $foreignResource->getIdentifier();
        $label = $foreignResource->getLabelField();

        foreach ($items as $item) {
            $options[$item->{$primary}] = $item->{$label};
        }

        return ['' => __('Please select')] + $options;
    }

    /**
     * @param null $resource
     * @param null $value
     *
     * @return Exception|DatabaseException|ErrorException|null
     * @throws BindingResolutionException
     * @throws BindingResolutionException
     */
    public function resolve($resource = null, $value = null)
    {

        if ($item = $resource->getModel()->{$this->getMethod()}) {
            return $item->{$this->getForeignResource()->getLabelField()};
        }

        return null;
    }

    /**
     * @param string $direction
     *
     * @return $this|AbstractField
     * @throws BindingResolutionException
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    public function order(string $direction)
    {
        if ($this->isVirtual()) {
            throw new Exception('Sorry, I can\'t order by virtual fields.');
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            throw new Exception('The order direction can only be "asc" or '.
                '"desc". Received "'.$direction.'"'
            );
        }

        $this->ordered = $direction;

        $localTable = $this->getResource()->getModel()->getTable();
        $foreignTable = $this->getForeignResource()->getModel()->getTable();
        $foreignIdentifier = $this->getForeignResource()->getIdentifier();
        $foreignLabelField = $this->getForeignResource()->getLabelField();

        $this->resource->getQuery()
            ->join($foreignTable, $foreignTable . '.' . $foreignIdentifier, '=', $localTable . '.' . $this->getName())
            ->select($foreignTable . '.' . $foreignLabelField . ' as ' .$foreignTable . '_' . $foreignLabelField, $localTable . '.*')
            ->orderBy($foreignTable . '_' . $foreignLabelField, $direction)->limit(20);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function filter(string $value)
    {
        if ($this->isVirtual()) {
            throw new Exception('Sorry, I can\'t filter by virtual fields.');
        }

        $this->filtered = $value;
        $this->resource->getQuery()->where($this->getName(), '=', $value);

        return $this;
    }
}
