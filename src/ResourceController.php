<?php

namespace Maduser\Laravel\Resource;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Maduser\Laravel\Resource\Fields\AbstractField;
use Maduser\Laravel\Resource\Fields\BelongsToMany;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Views\ResourceDisplay;
use Maduser\Laravel\Resource\Views\ResourceTable;
use Maduser\Laravel\ViewModel\Container;
use Maduser\Ui\Blade\Dashmix\Views\Block\Block;
use Maduser\Ui\Blade\Dashmix\Views\ButtonLink;
use Maduser\Ui\Blade\Dashmix\Views\Block\FormBlock;
use Maduser\Ui\Blade\Dashmix\Views\Page;
use Maduser\Ui\Blade\Dashmix\Views\Table;
use ReflectionException;
use Throwable;

/**
 * Class ResourceController
 *
 * TODO: Create services for the clumsy stuff
 *
 * @package Maduser\Laravel\Resource\Resource
 */
class ResourceController extends AbstractController
{
    /**
     * Display the specified resource.
     *
     * @param string $resource
     * @param int    $id
     *
     * @return Htmlable|Page
     * @throws ReflectionException
     */
    public function show(string $resource, int $id)
    {
        // Load the given resource
        $resource = $this->resource->findOrFail($id);

        // Make an array of field name/value pairs
        $rows = ['main' => [], 'meta' => []];
        $resource->getFields()->each(function($field) use (&$rows) {
            /** @var AbstractField $field */
            if (!$field->isNotInContext(['show']) && !in_array($field->getType(), ['belongs_to_many', 'has_many'])) {
                $rows['main'][] = ['label' => $field->getLabel(), 'value' => $field->getValue()];
            }
            if ($field->isInContext(['meta'])) {
                $rows['meta'][] = ['label' => $field->getLabel(), 'value' => $field->getValue()];
            }
        });

        // Make with the array a table for the detailed view
        // of the resource main data
        $mainTable = Table::create([
            'data' => $rows['main'],
            'cssClasses' => 'table-outer-bordered table-striped table-condensed font-size-lg'
        ]);

        // Make with the array a table for the detailed view
        // of the resource meta data
        $metaTable = Table::create([
            'data' => $rows['meta'],
            'cssClasses' => 'table-outer-bordered table-striped table-condensed small'
        ]);

        // We will have several blocks on this page,
        // if the resource has BelongsToMany fields
        $container = Container::create();

        // Make a block to display the resource detail table
        $container->push(Block::create([
            'name' => $resource->getName(),
            'title' => $resource->getLabel() . ': ' . $resource->getField($resource->getLabelField())->getValue(),
            'content' => ResourceDisplay::create(['mainTable' => $mainTable, 'metaTable' => $metaTable]),
            'footer' => Container::create([
                ButtonLink::create([
                    'text' => 'Back',
                    'btnClass' => 'btn btn-default',
                    'url' => route('resource.index', [
                        $resource->getName()
                    ])
                ]),
                ButtonLink::create([
                    'text' => 'Edit',
                    'btnClass' => 'btn btn-info',
                    'url' => route('resource.edit', [
                        $resource->getName(),
                        $resource->getId()
                    ])
                ])
            ])
        ]));

        // Create foreach pivot table field its own block
        // with a table of the related resources
        $resource->getForeignFields()->each(function($field) use ($container, $resource) {
            /** @var BelongsToMany $field */
            if (! $field->isNotInContext(['show'])) {


                //$method = $resource->getModel()->{$field->getMethod()}()->getTable();
                // TODO: check multiple pagination on same page

                // Get the related models and convert them to resources
                $models = $field->resolve($field->getResource());
                $resources = $field->getForeignResource()->makeCollection($models);

                // Make a paginated table
                $table = ResourceTable::create(['resource' => $field->getForeignResource()]);
                //$table->setId($method);
                $table->setLabels($field->getForeignResource()->getFieldLabels());
                $table->setRows($resources);
                $table->setShowActions(false);
                $table->setShowFilters(false);
                $table->setShowButtonSort(false);

                // Wrap the table in a block
                $container->push(Block::create([
                    'name' => $field->getForeignResource()->getName(),
                    'title' => $field->getForeignResource()->getTitle() .' related to ' . $resource->getLabel() . ' "'. $resource->getField($resource->getLabelField())->getValue() . '"',
                    'content' => $table,
                ]));
            }
        });

        // Make a create button to place in the top right region of the page
        $buttonCreate = ButtonLink::create([
            'text' => __('Create new ' . $this->resource->getLabel()),
            'url' => route('resource.create', [$this->resource->getName()]),
            'icon' => 'fa fa-plus-circle',
            'btnClass' => 'btn btn-info',
            'wrapperClass' => ''
        ]);

        // Make the page with the created widgets and return it's view
        return Page::create([
            'title' => $this->getPageTitle(),
            'regionTopRight' => $buttonCreate,
            'widgets' => $container
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $resource
     * @param int    $id
     *
     * @return Htmlable|Page
     * @throws ReflectionException
     * @throws Exception
     * @throws Exception
     */
    public function edit(string $resource, int $id)
    {
        $resource = $this->resource->find($id);

        $container = Container::create();

        $resource->getFields()->each(function ($field) use ($container) {
            /** @var AbstractField $field */
            $field->isNotInContext(['form']) || $container->push($field);
        });

        $formBlock = FormBlock::create([
            'title' => 'Edit ' . $resource->getLabel() . ': ' . $resource->getField($resource->getLabelField())
                    ->getValue(),
            'name' => $resource->getName(),
            'content' => $container,
            'cancelUrl' => url()->previous(),
            'method' => 'PUT',
            'action' => route('resource.update', [
                'resource' => $resource->getName()
            ])
        ]);

        // Make a create button to place in the top right region of the page
        $buttonCreate = ButtonLink::create([
            'text' => __('Create new ' . $this->resource->getLabel()),
            'url' => route('resource.create', [$this->resource->getName()]),
            'icon' => 'fa fa-plus-circle',
            'btnClass' => 'btn btn-info',
            'wrapperClass' => ''
        ]);

        // Make the page with the created widgets and return it's view
        return Page::create([
            'title' => $this->getPageTitle(),
            'regionTopRight' => $buttonCreate,
            'widgets' => $formBlock
        ]);
    }

}
