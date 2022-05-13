<?php

namespace Maduser\Laravel\Resource\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use Maduser\Laravel\Auth\Eloquent\Permission;
use Maduser\Laravel\Resource\Resources;

class ResourceGatesRemove extends Command
{

    /**
     * @var string
     */
    protected $signature = 'resource:gates-remove';

    /**
     * @var string
     */
    protected $description = 'Remove the resource Gates from database';

    /**
     *
     * @throws \ReflectionException
     */
    public function handle()
    {
        foreach (Resources::all() as $resource) {
            $gates = [
                'Can read resource ' . $resource['resource']::label(),
                'Can create resource ' . $resource['resource']::label(),
                'Can update resource ' . $resource['resource']::label(),
                'Can delete resource ' . $resource['resource']::label(),
            ];

            foreach ($gates as $gate) {
                Permission::where('name', $gate)->delete();
            }

        }

    }
}
