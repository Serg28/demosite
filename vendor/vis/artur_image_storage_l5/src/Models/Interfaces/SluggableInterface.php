<?php

namespace Vis\ImageStorage;

interface SluggableInterface
{
    public function makeUniqueSlug();

    public function getSlug();

    public function setSlug();
}