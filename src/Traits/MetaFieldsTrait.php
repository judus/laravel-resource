<?php

namespace Maduser\Laravel\Resource\Traits;

use Maduser\Laravel\Auth\Resources\User;
use Maduser\Laravel\Resource\Fields\BelongsTo;
use Maduser\Laravel\Resource\Fields\DateTime;

/**
 * Trait MetaFieldsTrait
 *
 * @package Maduser\Laravel\Resource\Traits
 */
trait MetaFieldsTrait
{
    /**
     * @return array
     */
    public function getMetaFields()
    {
        return [
            BelongsTo::create()
                ->setName('created_by')
                ->setLabel('Created by')
                ->setForeignResource(User::class)
                ->setMethod('createdBy')
                ->addInContext('meta')
                ->addNotInContext('form')
                ->addNotInContext('show')
                ->isFilterable(true)
                ->isSortable(true),

            DateTime::create()
                ->setName('created_at')
                ->setLabel('Created at')
                ->addInContext('meta')
                ->addNotInContext('form')
                ->addNotInContext('show')
                ->isFilterable(true)
                ->isSortable(true),

            BelongsTo::create()
                ->setName('updated_by')
                ->setLabel('Updated by')
                ->setForeignResource(User::class)
                ->setMethod('updatedBy')
                ->addInContext('meta')
                ->addNotInContext('form')
                ->addNotInContext('show')
                ->isFilterable(true)
                ->isSortable(true),

            DateTime::create()
                ->setName('updated_at')
                ->setLabel('Updated at')
                ->addInContext('meta')
                ->addNotInContext('form')
                ->addNotInContext('show')
                ->isFilterable(true)
                ->isSortable(true),

            BelongsTo::create()
                ->setName('deleted_by')
                ->setLabel('Deleted by')
                ->setForeignResource(User::class)
                ->setMethod('deletedBy')
                ->addInContext('meta')
                ->addNotInContext('form')
                ->addNotInContext('table')
                ->addNotInContext('show')
                ->isFilterable(true)
                ->isSortable(true),

            DateTime::create()
                ->setName('deleted_at')
                ->setLabel('Deleted at')
                ->addInContext('meta')
                ->addNotInContext('form')
                ->addNotInContext('table')
                ->addNotInContext('show')
                ->isFilterable(true)
                ->isSortable(true),
        ];
    }
}
