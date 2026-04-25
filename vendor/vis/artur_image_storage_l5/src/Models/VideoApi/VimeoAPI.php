<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Cache;

class VimeoAPI extends AbstractVideoAPI
{
    protected $configPrefix = 'video_api.providers.vimeo';

    public function videoExists()
    {
        $url = $this->getConfigAPIExistenceUrl();

        $queryParams = [
            'url'    => $this->getWatchUrl()
        ];

        $this->curl()->setRequestUrl($url, $queryParams)->doCurlRequest();

        if (!$this->curl()->isSuccessful()) {
            return false;
        }

        return true;
    }

    public function getPreviewUrl()
    {
        $tag       = $this->getConfigNamespace() . '.video';
        $cacheName = $this->getConfigPrefix() . "." . $this->getVideoId() . ".preview-id";

        $imageId = Cache::tags($tag)->rememberForever($cacheName, function () {

            $url = $this->getConfigAPIExistenceUrl();

            $queryParams = [
                'url' => $this->getWatchUrl()
            ];

            $this->curl()->setRequestUrl($url, $queryParams)->doCurlRequest();

            if (!$this->curl()->isSuccessful()) {
                return false;
            }

            $result = json_decode($this->curl()->getCurlResponseBody());

            preg_match('~video/(.*?)_~', $result->thumbnail_url, $imageId);


            return $imageId[1];
        });

        $stubs = ["{id}", "{quality}"];

        $replacements = [$imageId, $this->getConfigAPIPreviewQuality()];

        $url = str_replace($stubs, $replacements, $this->getConfigAPIPreviewUrl());

        return $url;
    }

    public function requestApiData()
    {
        $url = $this->getConfigAPIURL();

        $fields = ['fields' => $this->getConfigAPIParts()];

        $this->curl()
            ->setRequestHeader('Authorization', 'Bearer ' . $this->getConfigAPIKey())
            ->setRequestUrl($url . $this->getVideoId(), $fields)->doCurlRequest();

        if (!$this->curl()->isSuccessful()) {
            return false;
        }

        $apiData = json_decode($this->curl()->getCurlResponseBody());

        return $apiData;
    }

    //todo rewrite to ??(coalesce) operator
    public function getTitle()
    {
        return isset($this->getApiResponse()->name) ? $this->getApiResponse()->name : "";
    }

    public function getDescription()
    {
        return isset($this->getApiResponse()->description) ? $this->getApiResponse()->description : "";
    }

    public function getViewCount()
    {
        return isset($this->getApiResponse()->stats->plays) ? $this->getApiResponse()->stats->plays : 0;
    }

    public function getLikeCount()
    {
        return isset($this->getApiResponse()->metadata->connections->likes->total) ? $this->getApiResponse()->metadata->connections->likes->total : 0;
    }

    public function getDislikeCount()
    {
        return 0;
    }

    public function getFavoriteCount()
    {
        return 0;
    }

    public function getCommentCount()
    {
        return isset($this->getApiResponse()->metadata->connections->comments->total) ? $this->getApiResponse()->metadata->connections->comments->total : 0;
    }

}
