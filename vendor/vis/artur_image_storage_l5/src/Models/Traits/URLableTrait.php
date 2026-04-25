<?php

namespace Vis\ImageStorage;

trait URLableTrait
{
    public function getUrl()
    {
        return route($this->getTable() . "_show_single", [$this->getSlug()]);
    }
}