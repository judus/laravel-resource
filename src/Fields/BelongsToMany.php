<?php

namespace Maduser\Laravel\Resource\Fields;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use ReflectionException;

/**
 * Class BelongsToMany
 *
 * @package Maduser\Laravel\Resource\Fields
 */
class BelongsToMany extends AbstractField
{
    /**
     * BelongsToMany constructor.
     *
     * @param array $definition
     *
     * @throws ReflectionException
     */
    public function __construct(array $definition = [])
    {
        parent::__construct($definition);

        $this->setInputType('select-multiple');
        $this->setDbFieldType('VARCHAR(255)');

        $this->setFormat(function ($value, $field, $resource) {

            $items = $field->getRaw();

            foreach ($items as $id => &$item) {
                $url = route('resource.show', [$field->getForeignResource()->getName(), $id]);
                $text = $item;
                $item = new HtmlString('<a href="'.$url.'">'.$text.'</a>');
            }

            return implode(', ', $items);
        });

//        $this->setOptions(function () {
//            return $this->getOptions();
//        });
    }

    /**
     * @return array|mixed
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function getOptions()
    {
//        $options = [];
//
//        $foreignResource = $this->getForeignResource();
//        $model = $foreignResource->getModel();
//        $primary = $foreignResource->getIdentifier();
//        $label = $foreignResource->getLabelField();
//
//        $items = $model->select($primary, $label)->orderBy($label)->get();
//
//        foreach ($items as $item) {
//            $options[$item->{$primary}] = $item->{$label};
//        }
//
//        return $options;


        $options = $this->getDefinition('options', [$this]);

        if (!is_null($options)) {
            return $options;
        }

        $items = $this->getForeignModels();

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
        $model = $foreignResource->getModel();
        $primary = $foreignResource->getIdentifier();
        $label = $foreignResource->getLabelField();

        $items = $model->orderBy($label)->get();

        return $items;


//        $foreignResource = $this->getForeignResource();

//        return $foreignResource->getSelectOptions($this->getResource(), $this, function () use ($foreignResource) {
//            $model = $foreignResource->getModel();
//            $primary = $foreignResource->getIdentifier();
//            $label = $foreignResource->getLabelField();
//
//            //return $model->select($primary, $label)->orderBy($label)->get();
//            return $model->orderBy($label)->get();
//        });

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
        $model = $foreignResource->getModel();
        $primary = $foreignResource->getIdentifier();
        $label = $foreignResource->getLabelField();

        $items = $model->select($primary, $label)->orderBy($label)->get();

        foreach ($items as $item) {
            $options[$item->{$primary}] = $item->{$label};
        }

        return $options;
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
     * @throws Exception
     */
    public function getRaw()
    {
        $selectedItems = [];

        $foreignResource = $this->getForeignResource();
        $primary = $foreignResource->getIdentifier();
        $label = $foreignResource->getLabelField();

        $items = $this->resolve($this->resource);

        foreach ($items as $item) {
            $selectedItems[$item->{$primary}] = $item->{$label};
        }

        return $selectedItems;
    }

    /**
     * @param  ResourceInterface  $resource
     *
     * @param  int  $perPage
     *
     * @return Collection
     * @throws Exception
     */
    public function resolve(ResourceInterface $resource = null, int $perPage = 20): Collection
    {
        $namespace = $this->getType() . '_' . $this->getName() . '_page';

        /** @var LengthAwarePaginator $paginator */
        $paginator = $resource->getModel()
            ->{$this->getMethod()}()
            ->paginate($perPage, ['*'], $namespace);

        $this->getForeignResource()->setPaginator($paginator);

        return $paginator->getCollection();
    }
}
