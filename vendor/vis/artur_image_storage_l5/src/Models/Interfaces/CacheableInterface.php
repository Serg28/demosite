<?php namespace Vis\ImageStorage;

interface CacheableInterface
{
    public function flushCache();

    public function flushCacheRelation(CacheableInterface $relation);
}
