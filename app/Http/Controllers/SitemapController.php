<?php

namespace App\Http\Controllers;

use App\Services\SitemapXml;
use Vis\Builder\Models\Language;

class SitemapController extends Controller
{
    public function index()
    {
        $data = (new SitemapXml())->getLinks();
        $languages = (new Language())->getLanguages()->pluck('language');

        $content = view('sitemap', compact('data', 'languages'))->render();

        return response($content)->header('Content-Type', 'text/xml; charset="utf-8"');
    }
}
