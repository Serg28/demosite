<?php
return array(

    //enable or disable api requests
    'enabled' => true,

    // enables caching of API results.
    //x - minutes, 0 - forever, false - disabled
    'cache_minutes' => 60,

    // use api data to fill in title & description
    'set_data' => true,

    'video_existence_error' => "Не удалось найти видео на {type} с идентификатором: {id}",

    //used in provider select at edit_form
    'provider_names' => array(
        'youtube' => 'Youtube',
        'vimeo'   => 'Vimeo',
    ),

    'providers' => array(

        'youtube' => array(

            //check if video exists on youtube. works without api_key
            'video_existence_url' => 'https://www.youtube.com/oembed',

            //gets preview image of video in defined quality. works without api_key
            'preview_url' => 'https://img.youtube.com/vi/{id}/{quality}.jpg',

            //possible values: 0, 1, 2, 3, default, default, mqdefault, hqdefault, sddefault, maxresdefault
            'preview_quality' => 'maxresdefault',

            'watch_url' => 'https://www.youtube.com/watch?v=',

            'embed_url' => 'https://www.youtube.com/embed/',

            //https://developers.google.com/youtube/v3/docs/videos/list
            'api_url' => 'https://www.googleapis.com/youtube/v3/videos',

            'api_part' => 'snippet,statistics',

            //https://developers.google.com/youtube/v3/getting-started
            'api_key' => '',
        ),

        'vimeo' => array(

            //check if video exists on vimeo. works without api_key
            'video_existence_url' => 'https://vimeo.com/api/oembed.json',

            //gets preview image of video in defined quality. works without api_key
            //note: this is an image {id} not video {id} which is retrieved via video_existence_url
            'preview_url' => 'https://i.vimeocdn.com/video/{id}_{quality}.jpg',

            //possible values: 100, 200, 295, 640, 960, 1280
            'preview_quality' => '1280',

            'watch_url' => 'https://vimeo.com/',

            'embed_url' => 'https://player.vimeo.com/video/',

            //https://developer.vimeo.com/api/start
            'api_url' => 'https://api.vimeo.com/videos/',

            //leave empty to get everything
            'api_part' => 'name,description,duration, stats.plays, metadata.connections.comments.total, metadata.connections.likes.total',

            //https://developer.vimeo.com/apps/new -> Generate a new Access Token
            'api_key' => '',
        ),
    ),
);
