<?php namespace Vis\ImageStorage;

interface ConfigurableAPIInterface
{
    public function getConfigAPIEnabled();

    public function getConfigAPICacheMinutes();

    public function getConfigAPISetData();

    public function getConfigApiVideoExistenceError();

    public function getConfigAPIExistenceUrl();

    public function getConfigAPIPreviewUrl();

    public function getConfigAPIPreviewQuality();

    public function getConfigWatchUrl();

    public function getConfigEmbedUrl();

    public function getConfigAPIURL();

    public function getConfigAPIKey();

    public function getConfigAPIParts();
}
