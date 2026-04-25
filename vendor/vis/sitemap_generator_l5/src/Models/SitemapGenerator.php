<?php

namespace Vis\SitemapGenerator;

/**
 * Class SitemapGenerator
 * @package Vis\SitemapGenerator
 */
class SitemapGenerator
{
    /**
     * Stores all links
     * @var array
     */
    private $links = [];

    /**
     * Returns value from sitemap config
     * @param string $value
     * @return mixed
     */
    public function getConfigValue($value)
    {
        return config('sitemap-generator.sitemap.' . $value);
    }

    /**
     * Returns all links
     * @return mixed
     */
    private function getLinks()
    {
        return $this->links;
    }

    /**
     * Merges array to array of links
     * @param array $links
     */
    private function addToLinks(array $links)
    {
        $this->links = array_merge($this->links, $links);
    }

    /**
     * Returns model name depending on config key
     * @param  string $type
     * @return string $modelName
     */
    private function getModelName($type)
    {
        switch ($type) {
            case 'models':
                $modelName = "SitemapModel";
                break;
            case 'custom_links':
                $modelName = "SitemapLink";
                break;
            default:
                $modelName = null;
                break;
        }

        if (!$modelName) {
            return false;
        }

        return __NAMESPACE__ . '\\' . $modelName;
    }

    /**
     * Handles all entities in sitemap config
     * @param  string $type
     * @return boolean
     */
    private function handleLinks($type)
    {
        $entities  = $this->getConfigValue($type);
        $typeModel = $this->getModelName($type);

        if (empty($entities) || !$typeModel) {
            return false;
        }

        foreach ($entities as $key => $params) {
            $links = (new $typeModel($key, $params))->getLinksArray();
            $this->addToLinks($links);
        }

        return true;
    }

    /**
     * Entry point to sitemap generator
     * @return mixed
     */

    /*
     * fixme
     * instead of current realization where 'models' and 'custom_links' as separate sub-arrays
     * all entities should have been listed in a single 'entities' array with 'type' key for modeltype definition
     * */
    public function makeSitemap()
    {
        $this->handleLinks('custom_links');
        $this->handleLinks('models');

        return $this->getLinks();
    }

}
