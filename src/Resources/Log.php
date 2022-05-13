<?php

namespace Maduser\Laravel\Resource\Resources;

use Maduser\Laravel\Resource\AbstractResource;
use Maduser\Laravel\Resource\Fields\DateTime;
use Maduser\Laravel\Resource\Fields\Primary;
use Maduser\Laravel\Resource\Fields\Text;
use Maduser\Laravel\Resource\Fields\Textarea;
use Maduser\Laravel\Support\Eloquent\Log as Model;

class Log extends AbstractResource
{
    /**
     * The unique name of this resource
     * This will be used as argument for the resource routes
     *
     * It can be left null, in this case the class basename
     * will be used.
     *
     * @var string
     */
    protected static $name = 'log';

    /**
     * Define a label for forms and single views
     *
     * @var string
     */
    protected static $label = 'Log';

    /**
     * Define a title for listing and tables
     *
     * @var string
     */
    protected static $title = 'Logs';

    /**
     * Define the field that should be used when labeling a record
     *
     * @var string
     */
    protected $labelField = 'id';

    /**
     * Define the default sorting order
     *
     * @var string
     */
    protected $defaultOrder = ['created_at' => 'desc', 'id' => 'desc'];

    /**
     * The amount of items when loading paged
     *
     * @var string
     */
    protected $perPage = 500;

    /**
     * A array model observer
     *
     * @var array
     */
    protected $observers = [];

    /**
     * The Eloquent Model this resource will use
     *
     * @return string
     */
    public static function model(): string
    {
        return Model::class;
    }

    /**
     * We have to remove the ResourceObserver in order to avoid an endless loop
     *
     * @return array
     */
    public function getObservers(): array
    {
        return [];
    }

    public function fields(): array
    {
        return [
            Primary::create()
                ->setName('id')
                ->setLabel('ID')
                ->isFilterable(true)
                ->isSortable(true)
                ->addNotInContext('table'),

            DateTime::create()
                ->setName('created_at')
                ->setLabel('Created at')
                ->addInContext('meta')
                ->addNotInContext('form')
                ->addNotInContext('show')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('instance')
                ->setLabel('Instance')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('channel')
                ->setLabel('Channel')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('level')
                ->setLabel('Level')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('level_name')
                ->setLabel('Level name')
                ->isFilterable(true)
                ->isSortable(true),

            Textarea::create()
                ->setName('message')
                ->setLabel('Message')
                ->isFilterable(true)
                ->isSortable(true),

            Textarea::create()
                ->setName('context')
                ->setLabel('Context')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('remote_addr')
                ->setLabel('Remote Addr')
                ->isFilterable(true)
                ->isSortable(true),

            Text::create()
                ->setName('user_agent')
                ->setLabel('User Agent')
                ->isFilterable(true)
                ->isSortable(true)
        ];
    }
}
