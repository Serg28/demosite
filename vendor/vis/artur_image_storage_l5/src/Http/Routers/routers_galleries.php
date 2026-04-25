<?php
Route::any(
    'galleries', array(
        'as' => 'galleries_all',
        'uses' => 'GalleriesController@fetchIndex'
    )
);

if (Request::ajax()) {

    Route::post(
        'galleries/delete', array(
            'as' => 'delete_gallery',
            'uses' => 'GalleriesController@doDelete'
        )
    );

    Route::post(
        'galleries/get_form', array(
            'as' => 'get_gallery_edit_form',
            'uses' => 'GalleriesController@getForm'
        )
    );

    Route::post(
        'galleries/save_info', array(
            'as' => 'save_gallery_info',
            'uses' => 'GalleriesController@doSaveInfo'
        )
    );

    Route::post(
        'galleries/change_order', array(
            'as' => 'change_image_order',
            'uses' => 'GalleriesController@doChangeGalleryOrder'
        )
    );

    Route::post(
        'galleries/delete_relation', array(
            'as' => 'delete_image_relation',
            'uses' => 'GalleriesController@doDetachToGallery'
        )
    );

    Route::post(
        'galleries/set_gallery_preview', array(
            'as' => 'set_gallery_image_preview',
            'uses' => 'GalleriesController@doSetGalleryPreview'
        )
    );


    Route::post(
        'galleries/create_gallery_with', array(
            'as' => 'create_gallery_with_images',
            'uses' => 'GalleriesController@doCreateGalleryWith'
        )
    );

    Route::post(
        'galleries/add_array_to_galleries', array(
            'as' => 'add_images_to_galleries',
            'uses' => 'GalleriesController@doAddArrayToGalleries'
        )
    );


}
