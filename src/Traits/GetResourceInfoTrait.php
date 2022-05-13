<?php

namespace Maduser\Laravel\Resource\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Maduser\Laravel\Resource\Contracts\ResourceInterface;
use Maduser\Laravel\Resource\Contracts\ResourceModelInterface;

trait GetResourceInfoTrait
{
    /**
     * Get a Resource object from the given object
     *
     * @param ResourceModelInterface $model
     *
     * @return ResourceInterface
     * @throws Exception
     */
    protected function getResource(ResourceModelInterface $model
    ): ResourceInterface {

        if (empty($model->getResourceClass())) {
            throw new Exception('The model ' .get_class($model) . ' has no associated resource class');
        }
        return $model->getResourceClass()::make($model);
    }

    /**
     * @param  ResourceInterface  $resource
     *
     * @return mixed
     * @throws Exception
     */
    protected function getLabel(ResourceInterface $resource)
    {
        return $resource->getField($resource->getLabelField())->getValue();
    }

    /**
     * @param  ResourceModelInterface  $model
     * @param  string  $relation
     *
     * @return ResourceInterface
     */
    protected function getRelatedResource(
        ResourceModelInterface $model,
        string $relation
    ): ResourceInterface {
        /** @var ResourceModelInterface $relatedModel */
        $relatedModel = $model->{$relation}()->getModel();
        return $relatedModel->getResourceClass()::create();
    }

    /**
     * @param  ResourceInterface  $relatedResource
     * @param  array  $ids
     *
     * @return array
     * @throws Exception
     */
    protected function getRelatedItems(
        ResourceInterface $relatedResource,
        array $ids
    ): array {
        $items = $relatedResource->findMany($ids);

        $labels = [];
        foreach ($items as $item) {
            /** @var ResourceInterface $item */
            $labels[] = $item->getField($item->getLabelField())->getValue();
        }

        return [$items, $labels];
    }

    /**
     * @param  Model  $model
     *
     * @return array
     * @throws Exception
     */
    public function getInfos(Model $model)
    {
        $resource = $this->getResource($model);
        $type = $resource::label();
        $label = $this->getLabel($resource);

        return [$resource, $type, $label];
    }

    /**
     * @param  Model  $model
     * @param  string  $relation
     * @param  array  $ids
     *
     * @return array
     * @throws Exception
     */
    protected function getInfosWithRelation(
        Model $model,
        string $relation,
        array $ids
    ): array {
        list($resource, $label) = $this->getInfos($model);
        $relatedResource = $this->getRelatedResource($model, $relation);

        list($relatedResources, $relatedLabels) =
            $this->getRelatedItems($relatedResource, $ids);

        $relatedResourcesLabelString =
            $relatedResource->getLabel() . ' "'.implode('", "', $relatedLabels).'"';

        return [$resource, $label, $relatedResources, $relatedResourcesLabelString];
    }
}
