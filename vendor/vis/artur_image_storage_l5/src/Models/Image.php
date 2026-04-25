<?php namespace Vis\ImageStorage;

use Illuminate\Database\Eloquent\Builder;
use Vis\Builder\OptmizationImg;
use \Image as InterventionImage;
use Exception;


class Image extends AbstractImageStorageFile
{
    protected $table = 'vis_images';
    protected $configPrefix = 'image';
    protected $relatableList = ['galleries', 'tags'];

    protected static function boot()
    {
        parent::boot();
    }

    public function galleries()
    {
        return $this->belongsToMany('Vis\ImageStorage\Gallery', 'vis_images2galleries', 'id_image', 'id_gallery');
    }

    public function scopeFilterByGalleries(Builder $query, array $galleries = [])
    {
        if (!$galleries) {
            return $query;
        }

        $relatedImagesIds = self::whereHas('galleries', function (Builder $query) use ($galleries) {
            $query->whereIn('id_gallery', $galleries);
        })->pluck('id');

        return $query->whereIn('id', $relatedImagesIds);
    }

    public function makeFile($size = 'source')
    {
        $extension = $this->makeExtension($size);

        if ($extension == 'svg') {
            return parent::makeFile($size);
        }

        $sourceFile = $this->sourceFile ? $this->sourceFile->getRealPath() : $this->getPublicPath();

        $img = InterventionImage::make($sourceFile);

        $info = $this->getConfigSizeInfo($size);

        if (isset($info['modify'])) {
            foreach ($info['modify'] as $method => $args) {
                call_user_func_array([$img, $method], $args);
                if ($method == 'resizeCanvas' && (isset($args[4]) && preg_match('~rgba\(\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*[01]+\.?[0-9]?\)~', $args[4]))) {
                    $extension = 'png';
                }
            }
        }

        $destinationPath = public_path() . $this->makeFolders($size);
        $fileName = $this->makeFileName() . "." . $extension;

        $img->save($destinationPath . $fileName, $this->getConfigQuality());

        $this->{$this->sizePrefix . $size} = $size . "/" . $fileName;
        $this->sourceFile = null;

        return true;
    }

    public function optimizeImage($size)
    {
        if (!$this->getConfigOptimization()) {
            return false;
        }

        $sizes = $size ? [$size => ''] : $this->getConfigSizes();
        foreach ($sizes as $size => $info) {
            OptmizationImg::run("/" . $this->getSource($size));
        }

        return true;
    }

}
