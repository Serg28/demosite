<?php
Route::any(
    'videos', array(
        'as' => 'videos_all',
        'uses' => 'VideosController@fetchIndex'
    )
);

if (Request::ajax()) {

    Route::post(
        'videos/delete', array(
            'as' => 'delete_video',
            'uses' => 'VideosController@doDelete'
        )
    );

    Route::post(
        'videos/delete_multiple', array(
            'as' => 'delete_multiple_videos',
            'uses' => 'VideosController@doDeleteMultiple'
        )
    );

    Route::post(
        'videos/get_form', array(
            'as' => 'get_video_form',
            'uses' => 'VideosController@getForm'
        )
    );

    Route::post(
        'videos/save_info', array(
            'as' => 'save_video_info',
            'uses' => 'VideosController@doSaveInfo'
        )
    );

    Route::post(
        'videos/change_multiple_activity', array(
            'as' => 'change_multiple_activity_images',
            'uses' => 'VideosController@doChangeActivity'
        )
    );

    Route::post(
        'videos/load_more', array(
            'as' => 'load_more_videos',
            'uses' => 'VideosController@doLoadMoreEndless'
        )
    );

    Route::post(
        'videos/upload_video_preview', array(
            'as' => 'upload_video_preview',
            'uses' => 'VideosController@doUploadPreviewImage'
        )
    );

    Route::post(
        'videos/remove_video_preview', array(
            'as' => 'remove_video_preview',
            'uses' => 'VideosController@doRemovePreviewImage'
        )
    );
}
