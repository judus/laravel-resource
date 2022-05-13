<?php

namespace Maduser\Laravel\Resource\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use Maduser\Laravel\Auth\Eloquent\Permission;
use Maduser\Laravel\Resource\Resources;

class ResourceGatesPush extends Command
{

    /**
     * @var string
     */
    protected $signature = 'resource:gates-push';

    /**
     * @var string
     */
    protected $description = 'Load the resource Gates into database';

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
                Permission::unguard();
                Permission::updateOrCreate(
                    ['name' => $gate]
                );
            }

        }

    }
}
