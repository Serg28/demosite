<?php

namespace App\Cms\Services;

use Vis\Builder\Services\Listing;

class ListingOrders extends Listing
{
    private $definition;

    public function __construct($definition)
    {
        parent::__construct($definition);
        $this->definition = $definition;
    }

    public function actions()
    {
        return new ActionsOrders($this->definition);
    }
}
