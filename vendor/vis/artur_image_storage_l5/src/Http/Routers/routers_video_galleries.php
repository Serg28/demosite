<?php
Route::any(
    'video_galleries', array(
        'as' => 'video_galleries_all',
        'uses' => 'VideoGalleriesController@fetchIndex'
    )
);

if (Request::ajax()) {

    Route::post(
        'video_galleries/delete', array(
            'as' => 'delete_video_gallery',
            'uses' => 'VideoGalleriesController@doDelete'
        )
    );

    Route::post(
        'video_galleries/get_form', array(
            'as' => 'get_video_gallery_edit_form',
            'uses' => 'VideoGalleriesController@getForm'
        )
    );

    Route::post(
        'video_galleries/save_info', array(
            'as' => 'save_video_gallery_info',
            'uses' => 'VideoGalleriesController@doSaveInfo'
        )
    );

    Route::post(
        'video_galleries/change_order', array(
            'as' => 'change_image_order',
            'uses' => 'VideoGalleriesController@doChangeGalleryOrder'
        )
    );

    Route::post(
        'video_galleries/delete_relation', array(
            'as' => 'delete_image_relation',
            'uses' => 'VideoGalleriesController@doDetachToGallery'
        )
    );
    Route::post(
        'video_galleries/set_gallery_preview', array(
            'as' => 'set_gallery_video_preview',
            'uses' => 'VideoGalleriesController@doSetGalleryPreview'
        )
    );


    Route::post(
        'video_galleries/create_gallery_with', array(
            'as' => 'create_gallery_with_videos',
            'uses' => 'VideoGalleriesController@doCreateGalleryWith'
        )
    );

    Route::post(
        'video_galleries/add_array_to_galleries', array(
            'as' => 'add_videos_to_galleries',
            'uses' => 'VideoGalleriesController@doAddArrayToGalleries'
        )
    );


}
