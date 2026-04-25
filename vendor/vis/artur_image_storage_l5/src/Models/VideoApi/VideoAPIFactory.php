<?php namespace Vis\ImageStorage;

use Illuminate\Database\Eloquent\Model;

class VideoAPIFactory extends Model
{
    public static function makeAPI($type)
    {
        switch ($type) {
            case 'youtube':
                return new YoutubeAPI();
            case 'vimeo':
                return new VimeoAPI();
            default:
                throw new \InvalidArgumentException("Supported API provider should be called");
        }
    }
}
