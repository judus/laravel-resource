<?php

namespace Maduser\Laravel\Resource;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Traits\ObservableResourceTrait;
use ReflectionException;

/**
 * Class Resources
 *
 * This holds an array of resources for the service provider
 * to search and inject resources in the ResourceController.
 *
 * The get() method returns a new instance of a requested resource.
 *
 * @package Maduser\Laravel\Resource\Resource
 */
class Resources
{
    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @var array
     */
    protected static $resources = [];

    /**
     * Register one or an array of ResourceInterfaces
     *
     * @param mixed $resources
     *
     * @param bool  $skipObservers
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public static function register($resources, $skipObservers = false)
    {
        is_array($resources) || $resources = [$resources];

        if (empty(static::$config)) {
            static::$config = config('resources');

            if (config('resources.guard.enabled') === true) {
                foreach(static::$config['routing'] as &$route) {
                    $route['middleware'] = array_merge(
                        $route['middleware'],
                        $route['guard']
                    );
                }
            }
        }

        foreach ($resources as $resource => $config) {
            /** @var ResourceInterface $resource */

           if (! is_array($config)) {
                $resource = $config;
                $config = [];
            }

            // Make sure the resource implements the interface
            self::validate($resource);

            // Register the custom route if any
            if (isset($config['routing']) && $config['routing']) {
                $config['routing'] = self::route($resource, $config['routing']);
            } else {
                $config['routing'] = static::$config['routing'];
            }

            // Add the resource to the registry

            static::$resources[$resource::name()] = [
                'resource' => $resource,
                'config' => $config
            ];

            // Register the observers when the app does not run in the console
            if (! $skipObservers && in_array(
                ObservableResourceTrait::class, class_uses($resource)
            )) {
                $resource::model()::observe($resource::observers());
            }
        }
    }

    /**
     * Throws an exception if the given class does not
     * implement the ResourceInterface
     *
     * @param string $class
     *
     * @throws Exception
     */
    public static function validate(string $class)
    {
        if (!in_array(
            ResourceInterface::class,
            class_implements($class)
        )) {
            throw new Exception(
                'The given resource "' . $class . '" does not implement the ' .
                ResourceInterface::class
            );
        }
    }

    public static function route($resource, $routes)
    {
        foreach ($routes as $name => $customRoute) {
            if (isset(static::$config['routing'][$name])) {
                $customRoute = self::mergeRoute(
                    $resource,
                    static::$config['routing'][$name],
                    $customRoute
                );
            }

            if (isset($customRoute['guard']) && is_array($customRoute['guard'])) {
                foreach ($customRoute['guard'] as &$guard) {
                    $guard = str_replace('{resource}', $resource::name(), $guard);
                }
            }

            static::$config['routing'] = Arr::prepend(
                static::$config['routing'],
                $customRoute,
                $name . '.' . $resource::name()
            );
        }

        return static::$config['routing'];
    }

    public static function loadRoutes()
    {
        Route::middleware('web')->group(function() {

            foreach (static::$config['routing'] as $key => $resource) {

                $route = Route::match($resource['method'], $resource['pattern'], $resource['call']);

                $route->name($key);

                if (isset($resource['where']) && !empty($resource['where'])) {
                    $route->where($resource['where']);
                }

                if (isset($resource['middleware']) && !empty($resource['middleware'])) {
                    $route->middleware($resource['middleware']);
                }
            }

        });
    }

    /**
     * Returns a collection of all the resources in the array
     *
     * @return Collection|ResourceInterface[]
     */
    public static function all(): Collection
    {
        return collect(static::$resources);
    }

    /**
     * Checks if a resource exists
     *
     * @param string $name
     *
     * @return bool
     */
    public static function exists(string $name): bool
    {
        return isset(static::$resources[$name]);
    }

    /**
     * Returns a new instance of a ResourceInterface object
     *
     * @param string $name
     * @param array  $args
     *
     * @return ResourceInterface
     * @throws BindingResolutionException
     */
    public static function get(string $name, array $args = []): ResourceInterface
    {
        return app()->make(static::$resources[$name]['resource'], $args);
    }

    /**
     * @param $resource
     * @param $defaultRoute
     * @param $customRoute
     *
     * @return array
     */
    protected static function mergeRoute($resource, $defaultRoute, $customRoute): array
    {
        $mergedRoute = array_replace_recursive($defaultRoute, $customRoute);

        if (!isset($customRoute['pattern']) || empty($customRoute['pattern'])) {
            $mergedRoute['pattern'] = str_replace('{resource}', $resource::name(), $defaultRoute['pattern']);
        }

        if (!isset($customRoute['where']) || empty($customRoute['where'])) {
            Arr::forget($mergedRoute['where'], ['resource']);
        }

        return $mergedRoute;
}
}
