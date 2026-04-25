<?php

namespace Vis\ImageStorage;

trait RelatableTrait
{
    protected $relatableList = ['tags'];

    protected static function bootRelatableTrait()
    {
        static::saved(function (RelatableInterface $item) {
            $item->makeRelations();
        });
    }

    public function tags()
    {
        return $this->morphToMany('Vis\ImageStorage\Tag', 'entity', 'vis_tags2entities', 'id_entity', 'id_tag');
    }

    public function getRelatableList()
    {
        return $this->relatableList;
    }

    public function relationExists($relation)
    {
        return method_exists($this, $relation);
    }

    public function getRelatedClass($relation)
    {
       return $this->$relation()->getRelated();
    }

    public function getRelationClassName($relation)
    {
        return get_class($this->getRelatedClass($relation));
    }

    public function getRelatedEntities()
    {
        $relatedEntities = [];

        foreach ($this->getRelatableList() as $relation) {
            if ($this->relationExists($relation)) {
                $relatedClassName = $this->getRelationClassName($relation);
                $relatedEntities[$relation] = $relatedClassName::active()->orderId()->get();
            }
        }

        return $relatedEntities;
    }

    protected function makeRelations()
    {
        foreach ($this->getRelatableList() as $relation) {
            if ($this->relationExists($relation)) {

                $relationRequest = request('relations');

                $relatedEntities =  isset($relationRequest['image-storage-'. $relation]) ? $relationRequest['image-storage-'. $relation] : [];
                $this->$relation()->sync($relatedEntities);

                //fixme add clear cache
                $this->flushCache();
                $this->flushCacheRelation($this->getRelatedClass($relation));
            }
        }
    }

}