<?php

namespace Litvin\Redirectmap\Controllers;

use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Litvin\Redirectmap\RedirectImport;

/**
 * Class DashboardController.
 */
class ImportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function __invoke()
    {

        Excel::import(new RedirectImport, request()->file('file'));

        return [
          'status' => 'success'
        ];
    }
}
