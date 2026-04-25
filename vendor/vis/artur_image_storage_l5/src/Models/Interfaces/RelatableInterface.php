<?php

namespace Vis\ImageStorage;

interface RelatableInterface
{
    public function tags();

    public function getRelatableList();

    public function relationExists($relation);

    public function getRelationClassName($relation);
    
    public function getRelatedEntities();


}