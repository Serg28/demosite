<?php

namespace Vis\SitemapGenerator;

/**
 * Class AbstractSitemapObject
 * @package Vis\SitemapGenerator
 */
abstract class AbstractSitemapObject
{
    /**
     * Url property
     * @var string
     */
    protected $url = '';

    /**
     * Change frequency property
     * @var string
     */
    protected $changefreq = 'weekly';

    /**
     * Priority property
     * @var string
     */
    protected $priority = '0.7';

    /**
     * Lastmod property
     * @var string
     */
    protected $lastmod = '';

    /**
     * AbstractSitemapObject constructor
     * @param string $key
     * @param string $params
     */
    public function __construct($key, $params)
    {
        $this->setKey($key)->setParams($params);
    }

    /**
     * Abstract method to detect which property should be filled in with config key
     * @param $key
     * @return mixed
     */
    abstract protected function setKey($key);

    /**
     * Sets all params
     * @param array $params
     * @return $this
     */
    protected function setParams($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
        } else {
            $this->setKey($params);
        }

        return $this;
    }

    /**
     * Returns url property
     * @return string
     */
    private function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns changefreq property
     * @return string
     */
    private function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * Returns priority property
     * @return int
     */
    private function getPriority()
    {
        return $this->priority;
    }

    /**
     * Returns lasmod property
     * @return string
     */
    private function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * Returns asset url property
     * @return string
     */
    private function getAssetUrl()
    {
        return asset($this->getUrl());
    }

    /**
     * Returns last mod date property in ISO 8601 date format
     * @return string
     */
    private function getLastmodDate()
    {
        if (!$this->getLastmod()) {
            return false;
        }

        return date("c", strtotime($this->getLastmod()));
    }

    /**
     * Gets alternate URLs for multiple languages
     * @return array $alternateUrls
     */
    private function getAlternateUrls()
    {
        $alternateUrls = [];
        $isMulti = config('sitemap-generator.sitemap.is_multi_language');

        if ($isMulti) {
            $languages = \LaravelLocalization::getSupportedLocales();
            foreach ($languages as $language => $description) {
                $alternateUrls[] = [
                    'hreflang' => $language == 'ua' ? 'uk' : $language,
                    'href'     => \LaravelLocalization::getLocalizedURL($language, $this->getAssetUrl()),
                ];
            }
        }

        return $alternateUrls;
    }

    /**
     * Converts data to proper array
     * @return array
     */
    protected function convertToArray()
    {
        return [
            'url'            => $this->getAssetUrl(),
            'alternate_urls' => $this->getAlternateUrls(),
            'date'           => $this->getLastmodDate(),
            'changefreq'     => $this->getChangefreq(),
            'priority'       => $this->getPriority(),
        ];
    }

    /**
     * Returns array of links
     * @return array
     */
    abstract public function getLinksArray();

}
