<?php

Route::group([
    'middleware' => ['web'],
    'namespace' => 'Vis\SitemapGenerator',
], function () {

    Route::get(
        'sitemap.xml', [
            'as' => 'show_sitemap',
            'uses' => 'SitemapGeneratorController@showSiteMapXML',
        ]
    );

});
