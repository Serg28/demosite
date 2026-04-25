<?php namespace Vis\ImageStorage;

use Illuminate\Database\Eloquent\Builder;

class VideoGallery extends AbstractImageStorageGallery
{
    protected $table = 'vis_video_galleries';
    protected $configPrefix = 'video_gallery';

    protected $galleryRelation = 'videos';

    public function videos()
    {
        return $this
            ->belongsToMany('Vis\ImageStorage\Video', 'vis_videos2video_galleries', 'id_video_gallery', 'id_video')
            ->orderBy('priority', 'desc')
            ->withPivot('is_preview');
    }

    public function scopeHasVideos(Builder $query)
    {
        return parent::scopeHasRelated($query);
    }

    public function scopeHasActiveVideos(Builder $query)
    {
        return parent::scopeHasRelatedActive($query);
    }

}
