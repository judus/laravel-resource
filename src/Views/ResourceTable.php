<?php

namespace Maduser\Laravel\Resource\Views;

use Illuminate\Support\Traits\ForwardsCalls;
use Maduser\Generic\Traits\SelfAwareClass;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\ViewModel\ViewModel;

class ResourceTable extends ViewModel
{
    protected $view = 'resource.resource-table';

    protected $exposedAs = 'table';

    protected $id;
    /**
     * @var ResourceInterface
     */
    protected $resource;

    protected $labels;

    protected $rows;

    protected $order = [null, null];

    protected $filters = [];

    protected $showLabels = true;

    protected $showActions = true;

    protected $showFilters = true;

    protected $showCheckboxes = false;

    protected $showButtonSort = true;

    protected $showButtonActivate = true;

    protected $showButtonView = true;

    protected $showButtonEdit = true;

    protected $showButtonDelete = true;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return ResourceTable
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param mixed $labels
     *
     * @return ResourceTable
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param mixed $rows
     *
     * @return ResourceTable
     */
    public function setRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return ResourceInterface
     */
    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return ResourceTable
     */
    public function setResource(ResourceInterface $resource): ResourceTable
    {
        $this->resource = $resource;

        return $this;
    }


    public function getOrder(): array
    {
        return $this->order;
    }

    public function setOrder(array $order): ResourceTable
    {
        if (!empty($order)) {
            $this->order = [array_keys($order)[0], reset($order)];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param  array  $filters
     *
     * @return ResourceTable
     */
    public function setFilters(array $filters): ResourceTable
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @return bool
     */
    public function getShowActions(): bool
    {
        return $this->showActions;
    }

    /**
     * @param bool $showActions
     *
     * @return ResourceTable
     */
    public function setShowActions(bool $showActions): ResourceTable
    {
        $this->showActions = $showActions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowCheckboxes()
    {
        return $this->showCheckboxes;
    }

    /**
     * @param mixed $showCheckboxes
     *
     * @return ResourceTable
     */
    public function setShowCheckboxes($showCheckboxes)
    {
        $this->showCheckboxes = $showCheckboxes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowButtonSort()
    {
        return $this->showButtonSort;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->showButtonSort;
    }

    /**
     * @param mixed $showButtonSort
     *
     * @return ResourceTable
     */
    public function setShowButtonSort($showButtonSort)
    {
        $this->showButtonSort = $showButtonSort;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowButtonActivate()
    {
        return $this->showButtonActivate;
    }

    /**
     * @param mixed $showButtonActivate
     *
     * @return ResourceTable
     */
    public function setShowButtonActivate($showButtonActivate)
    {
        $this->showButtonActivate = $showButtonActivate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowButtonView()
    {
        return $this->showButtonView;
    }

    /**
     * @param mixed $showButtonView
     *
     * @return ResourceTable
     */
    public function setShowButtonView($showButtonView)
    {
        $this->showButtonView = $showButtonView;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowButtonEdit()
    {
        return $this->showButtonEdit;
    }

    /**
     * @param mixed $showButtonEdit
     *
     * @return ResourceTable
     */
    public function setShowButtonEdit($showButtonEdit)
    {
        $this->showButtonEdit = $showButtonEdit;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowButtonDelete()
    {
        return $this->showButtonDelete;
    }

    /**
     * @param mixed $showButtonDelete
     *
     * @return ResourceTable
     */
    public function setShowButtonDelete($showButtonDelete)
    {
        $this->showButtonDelete = $showButtonDelete;

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowLabels(): bool
    {
        return $this->showLabels;
    }

    /**
     * @param bool $showLabels
     *
     * @return ResourceTable
     */
    public function setShowLabels(bool $showLabels): ResourceTable
    {
        $this->showLabels = $showLabels;

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowFilters(): bool
    {
        return $this->showFilters;
    }

    /**
     * @param bool $showFilters
     *
     * @return ResourceTable
     */
    public function setShowFilters(bool $showFilters): ResourceTable
    {
        $this->showFilters = $showFilters;

        return $this;
    }


    public function hasRows()
    {
        if ($this->getRows()) {
            return $this->getRows()->count() > 0;
        }

        return false;
    }

    public function hasLabels(): bool
    {
        return $this->showLabels;
    }

    public function hasCheckboxes(): bool
    {
        return $this->getShowCheckboxes();
    }

    public function hasSortingButtons(): bool
    {
        return $this->getShowButtonSort();
    }

    public function hasFilters(): bool
    {
        return $this->showFilters;
    }

    public function hasActions(): bool
    {
        return $this->showActions;
    }

}
