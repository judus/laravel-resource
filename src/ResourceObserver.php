<?php

namespace Maduser\Laravel\Resource;

use App\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maduser\Laravel\Resource\Traits\GetResourceInfoTrait;

/**
 * Class ResourceObserver
 *
 * @package Maduser\Laravel\Resource\Resource
 */
class ResourceObserver
{
    use GetResourceInfoTrait;

    /**
     * @var User|Authenticatable|null
     */
    protected $user;

    /**
     * @var mixed|string
     */
    protected $username = 'Someone';

    /**
     * ResourceObserver constructor.
     */
    public function __construct()
    {
        if ($this->user = Auth::user()) {
            $this->username = $this->user->name;
        }
    }

    /**
     * Before creating the model
     *
     * @param  Model  $model
     */
    public function creating(Model $model)
    {
        if ($this->user) {
            $model->created_by = $this->user->getAuthIdentifier();
            $model->updated_by = $this->user->getAuthIdentifier();
        }
    }

    /**
     * After the model has been created
     *
     * @param Model $model
     *
     * @throws Exception
     */
    public function created(Model $model)
    {
        list($resource, $type, $label) = $this->getInfos($model);

        Log::info(
            $this->username . ' created a new ' . $type . ' "' . $label . '"'
        );
    }

    /**
     * Before updating the model
     *
     * @param  Model  $model
     */
    public function updating(Model $model)
    {
        if ($user = Auth::user()) {
            $model->updated_by = Auth::user()->getAuthIdentifier();
        }
    }

    /**
     * After the model has been updated
     *
     * @param Model $model
     *
     * @throws Exception
     */
    public function updated(Model $model)
    {
        list($resource, $type, $label) = $this->getInfos($model);

        Log::info(
            $this->username . ' updated the ' . $type . ' "' . $label . '"'
        );
    }

    /**
     * Before deleting the model
     *
     * @param Model $model
     *
     * @throws Exception
     */
    public function deleting(Model $model)
    {
        $model->deleted_by = Auth::user()->getAuthIdentifier();

        list($resource, $type, $label) = $this->getInfos($model);

        Log::info(
            $this->username . ' is deleting the ' . $type . ' "' . $label . '"'
        );
    }

    /**
     * After the model has been deleted
     *
     * @param  Model  $model
     *
     * @throws Exception
     */
    public function deleted(Model $model)
    {
        list($resource, $type, $label) = $this->getInfos($model);

        Log::info(
            $this->username . ' deleted the ' . $type
        );
    }

    /**
     * After a belongsToMany relationship has been attached
     *
     * @param $relation
     * @param Model $model
     * @param $ids
     *
     * @throws Exception
     */
    public function belongsToManyAttached($relation, Model $model, $ids)
    {
        list($resource, $label, $relatedResources, $relatedItemsString) =
            $this->getInfosWithRelation($model, $relation, $ids);

        Log::info(
            $this->username . ' attached ' . $relatedItemsString .
            ' to the ' . $resource->getLabel() . ' "' . $label . '"'
        );
    }

    /**
     * After a belongsToMany relationship has been detached
     *
     * @param $relation
     * @param $model
     * @param $ids
     *
     * @throws Exception
     */
    public function belongsToManyDetached($relation, $model, $ids)
    {
        list($resource, $label, $relatedResources, $relatedItemsString) =
            $this->getInfosWithRelation($model, $relation, $ids);

        Log::info(
            $this->username . ' detached ' . $relatedItemsString .
            ' from the ' . $resource->getLabel() . ' "' . $label . '"'
        );
    }
}
