<?php

namespace Vis\ImageStorage;

use Illuminate\Database\Eloquent\Builder;

interface GalleryInterface
{
    public function getGalleryRelation();

    public function scopeHasRelated(Builder $query);

    public function scopeHasRelatedActive(Builder $query);

    public function getPreview();

    public function setPreview($preview);

    public function changeGalleryOrder($idArray);

    public function relateToGallery($idArray);

    public function detachToGallery($id);
}