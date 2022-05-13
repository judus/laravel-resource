<?php

use Maduser\Laravel\Resource\ResourceController;

return [
    'guard' => [
        'enabled' => false,
    ],
    'routing' => [
        'resource.index' => [
            'pattern' => 'resources/{resource}',
            'where' => ['resource' => '\w+'],
            'call' => [ResourceController::class, 'index'],
            'middleware' => ['web', 'auth'],
            'method' => 'get',
            'guard' => ['can:can-read-resource,resource'],
        ],
        'resource.create' => [
            'pattern' => 'resources/{resource}/create',
            'where' => ['resource' => '\w+'],
            'call' => [ResourceController::class, 'create'],
            'middleware' => ['web', 'auth',],
            'method' => 'get',
            'guard' => ['can:can-create-resource,resource']
        ],
        'resource.store' => [
            'pattern' => 'resources/{resource}/store',
            'where' => ['resource' => '\w+'],
            'call' => [ResourceController::class, 'store'],
            'middleware' => ['web', 'auth'],
            'method' => 'post',
            'guard' => ['can:can-create-resource,resource']
        ],
        'resource.show' => [
            'pattern' => 'resources/{resource}/{id}',
            'where' => ['resource' => '\w+', 'id' => '[0-9]+'],
            'call' => [ResourceController::class, 'show'],
            'middleware' => ['web', 'auth'],
            'method' => 'get',
            'guard' => ['can:can-read-resource,resource']
        ],
        'resource.edit' => [
            'pattern' => 'resources/{resource}/{id}/edit',
            'where' => ['resource' => '\w+', 'id' => '[0-9]+'],
            'call' => [ResourceController::class, 'edit'],
            'middleware' => ['web', 'auth'],
            'method' => 'get',
            'guard' => ['can:can-update-resource,resource']
        ],
        'resource.update' => [
            'pattern' => 'resources/{resource}/update',
            'where' => ['resource' => '\w+'],
            'call' => [ResourceController::class, 'update'],
            'middleware' => ['web', 'auth'],
            'method' => 'put',
            'guard' => ['can:can-update-resource,resource']
        ],
        'resource.destroy' => [
            'pattern' => 'resources/{resource}',
            'where' => ['resource' => '\w+', 'id' => '[0-9]+'],
            'call' => [ResourceController::class, 'destroy'],
            'middleware' => ['web', 'auth'],
            'method' => 'delete',
            'guard' => ['can:can-delete-resource,resource']
        ],
        'resource.attach' => [
            'pattern' => 'resources/{resource}/attach',
            'where' => ['resource' => '\w+', 'id' => '[0-9]+'],
            'call' => [ResourceController::class, 'attach'],
            'middleware' => ['web', 'auth'],
            'method' => 'patch',
            'guard' => ['can:can-update-resource,resource']
        ],
        'resource.detach' => [
            'pattern' => 'resources/{resource}/detach',
            'where' => ['resource' => '\w+'],
            'call' => [ResourceController::class, 'detach'],
            'middleware' => ['web', 'auth'],
            'method' => 'patch',
            'guard' => ['can:can-update-resource,resource']
        ],
        'resource.filter' => [
            'pattern' => 'resources/{resource}',
            'where' => ['resource' => '\w+'],
            'call' => [ResourceController::class, 'index'],
            'middleware' => ['web', 'auth'],
            'method' => 'post',
            'guard' => ['can:can-read-resource,resource']
        ],
        'resource.reorder' => [
            'pattern' => 'resources/{resource}/{id}/reorder',
            'where' => ['resource' => '\w+', 'id' => '[0-9]+'],
            'call' => [ResourceController::class, 'reorder'],
            'middleware' => ['web', 'auth'],
            'method' => ['post'],
            'guard' => ['can:can-read-resource,resource']
        ],
    ]
];
