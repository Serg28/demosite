<?php
namespace Litvin\Redirectmap\Service;

use Vis\Builder\Interfaces\Button;
use Vis\Builder\Services\ButtonBase;
use Illuminate\Contracts\View\View;

class Import extends ButtonBase implements Button
{
    public function show() : View
    {
        return view('redirect::import');
    }
}