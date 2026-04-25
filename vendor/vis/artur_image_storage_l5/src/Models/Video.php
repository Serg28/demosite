<?php namespace Vis\ImageStorage;

use Illuminate\Database\Eloquent\Builder;

class Video extends AbstractImageStorage
{
    protected $table = 'vis_videos';
    protected $configPrefix = 'video';
    protected $relatableList = ['videoGalleries', 'tags'];

    protected $api;

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Video $item) {
            if (!$item->videoExists()) {
                return false;
            }

            $item->useApiData();
            $item->setSlug();
        });
    }

    public function api()
    {
        if (!$this->api) {
            $this->api = VideoAPIFactory::makeAPI($this->api_provider);
            $this->api->setVideoId($this->api_id);
        }

        return $this->api;
    }

    public function preview()
    {
        return $this->belongsTo('Vis\ImageStorage\Image', 'id_preview');
    }

    public function videoGalleries()
    {
        return $this->belongsToMany('Vis\ImageStorage\VideoGallery', 'vis_videos2video_galleries', 'id_video', 'id_video_gallery');
    }

    public function scopeFilterByVideoGalleries(Builder $query, array $galleries = [])
    {
        if (!$galleries) {
            return $query;
        }

        $relatedVideosId = self::whereHas('videoGalleries', function (Builder $query) use ($galleries) {
            $query->whereIn('id_video_gallery', $galleries);
        })->pluck('id');

        return $query->whereIn('id', $relatedVideosId);
    }

    public function videoExists()
    {
        if (!$this->api()->videoExists()) {
            $errorMessage = $this->api()->getExistenceErrorMessage();
            $this->setErrorMessage($errorMessage);
            return false;
        }

        return true;
    }

    protected function useApiData()
    {
        if (!$this->api()->getConfigAPISetData()) {
            return false;
        }

        $columnNames = $this->getConfigFieldsNames();

        foreach ($columnNames as $key => $columnName) {
            if (strpos($columnName, 'title') !== false && !$this->$columnName) {
                $this->$columnName = $this->api()->getTitle();
            } elseif (strpos($columnName, 'description') !== false && !$this->$columnName) {
                $this->$columnName = $this->api()->getDescription();
            }
        }
    }

    public function getSource()
    {
        return $this->api_id;
    }

    public function getPreviewImage($size = 'source')
    {
        return $this->id_preview ? $this->preview->getSource($size) : $this->api()->getPreviewUrl();
    }

    public function setPreviewImage($id)
    {
        $this->preview()->associate($id)->save();
    }

    public function unsetPreviewImage()
    {
        $this->preview()->dissociate()->save();
    }

}
