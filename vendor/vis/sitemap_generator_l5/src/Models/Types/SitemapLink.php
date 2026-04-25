<?php

namespace Vis\SitemapGenerator;

class SitemapLink extends AbstractSitemapObject
{
    /**
     * Sets config key to url property
     * @param string $key
     * @return $this
     */
    protected function setKey($key)
    {
        $this->url = $key;
        return $this;
    }

    /**
     * Returns array of links
     * @return array $links
     */
    public function getLinksArray()
    {
        $links[] = $this->convertToArray();

        return $links;
    }

}
