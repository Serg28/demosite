<?php namespace Vis\ImageStorage;

use Illuminate\Database\Eloquent\Builder;

class AbstractImageStorageGallery extends AbstractImageStorage implements GalleryInterface
{
    protected $galleryRelation = '';

    public function getGalleryRelation()
    {
        return $this->{$this->galleryRelation}();
    }

    public function scopeHasRelated(Builder $query)
    {
        return $query->has($this->galleryRelation);
    }

    public function scopeHasRelatedActive(Builder $query)
    {
        return $query->whereHas($this->galleryRelation, function (Builder $query) {
            $query->active();
        });
    }

    public function getPreview()
    {
        return $this->getGalleryRelation()->wherePivot("is_preview", "1")->first();
    }

    public function setPreview($preview)
    {
        if ($currentPreview = $this->getPreview()) {
            $this->getGalleryRelation()->updateExistingPivot($currentPreview->id, ["is_preview" => 0]);
        }

        $this->getGalleryRelation()->updateExistingPivot($preview, ["is_preview" => 1]);

        $this->flushCacheRelation($this->getGalleryRelation()->getRelated());
    }

    public function changeGalleryOrder($idArray)
    {
        $priority = count($idArray);

        foreach ($idArray as $id) {
            $this->getGalleryRelation()->updateExistingPivot($id, ['priority' => $priority]);
            $priority--;
        }

        $this->flushCacheRelation($this->getGalleryRelation()->getRelated());
    }

    public function relateToGallery($idArray)
    {
        $this->getGalleryRelation()->syncWithoutDetaching($idArray);

        $this->flushCacheRelation($this->getGalleryRelation()->getRelated());
    }

    public function detachToGallery($id)
    {
        $this->getGalleryRelation()->detach($id);

        $this->flushCacheRelation($this->getGalleryRelation()->getRelated());
    }

}
