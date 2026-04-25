<?php

namespace Vis\SitemapGenerator;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class SitemapGeneratorController extends Controller
{
    /**
     * Entry point for displaying sitemap
     * @return mixed
     */
    public function showSiteMapXML()
    {
        $sitemap = new SitemapGenerator();

        if (!$sitemap->getConfigValue('is_enabled')) {
            abort(404);
        }

        $view = View::make('sitemap::sitemap')->with('links', $sitemap->makeSitemap());

        return Response::make($view, '200')->header('Content-Type', 'text/xml; charset="utf-8"');
    }

}
