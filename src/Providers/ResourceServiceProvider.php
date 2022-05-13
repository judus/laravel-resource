<?php

namespace Maduser\Laravel\Resource\Providers;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Maduser\Laravel\Auth\Resources\User;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\ResourceController;
use Maduser\Laravel\Resource\Resources;
use Maduser\Laravel\Resource\Resources\Log;

class ResourceServiceProvider extends ServiceProvider
{
    /**
     * @throws Exception
     */
    public function boot()
    {
        $this->app['view']->addLocation(__DIR__ . '/../../resources/views');

        $this->mergeConfigFrom(__DIR__ .'/../../config/resources.php', 'resources');

        $this->registerResources();

        $this->app
            ->when(ResourceController::class)
            ->needs(ResourceInterface::class)
            ->give(function () {

                $resourceName = Request::segment(2);

                if (is_null($resourceName) && App::runningInConsole()) {
                    $resourceName = User::name();
                }

                if (Resources::exists($resourceName)) {
                    return Resources::get($resourceName);
                } else {
                    App::runningInConsole() || abort(404);
                }
            });
    }

    /**
     * Registers the resources from resources()
     *
     * @throws Exception
     * @throws Exception
     */
    public function registerResources()
    {
        Resources::register($this->resources(), $this->app->runningInConsole());

        $this->app->booted(function() {
            Resources::loadRoutes();
        });
    }

    /**
     * Declares the available Resource objects
     *
     * @return array
     */
    public function resources(): array
    {
        return [];
    }

}
