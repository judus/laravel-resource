<?php

namespace Maduser\Laravel\Resource;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use Maduser\Generic\Traits\JsonSerializableTrait;
use Maduser\Generic\Traits\SelfAwareClass;
use Maduser\Generic\Traits\SelfAwareEloquent;
use Maduser\Laravel\Resource\Fields\AbstractField;
use Maduser\Laravel\Resource\Fields\BelongsTo;
use Maduser\Laravel\Resource\Fields\BelongsToMany;
use Maduser\Laravel\Resource\Fields\FieldFactory;
use Maduser\Laravel\Resource\Fields\HasMany;
use Maduser\Laravel\Resource\Fields\Identifier;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Traits\MetaFieldsTrait;
use ReflectionException;

/**
 * Class AbstractResource
 *
 * @package Maduser\Laravel\Resource\Resource
 */
abstract class AbstractContentResource extends AbstractResource
{
    public static function create(): ResourceInterface
    {

    }
}
