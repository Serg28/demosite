<?php

namespace App\Http\Controllers;

use App\Models\MenuHeader;

class AjaxController extends BaseController {

    public function menuCatalog()
    {
        $topMenu = (new MenuHeader())->getMenu();
        $url = (request()->ajax()) ? (request()->header('referer') ?? request()->url()) : request()->url();
        return response()->json([
            'status' => 'success',
            'html' => view('partials.ajax_menucatalog', compact('topMenu', 'url'))->render(),
        ]);
    }
}
