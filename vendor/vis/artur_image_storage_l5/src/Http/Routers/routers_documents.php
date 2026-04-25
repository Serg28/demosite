<?php
Route::any(
    'documents', array(
        'as' => 'documents_all',
        'uses' => 'DocumentsController@fetchIndex'
    )
);

if (Request::ajax()) {

    Route::post(
        'documents/delete', array(
            'as' => 'delete_image',
            'uses' => 'DocumentsController@doDelete'
        )
    );

    Route::post(
        'documents/delete_multiple', array(
            'as' => 'delete_multiple_documents',
            'uses' => 'DocumentsController@doDeleteMultiple'
        )
    );

    Route::post(
        'documents/get_form', array(
            'as' => 'get_image_form',
            'uses' => 'DocumentsController@getForm'
        )
    );

    Route::post(
        'documents/save_info', array(
            'as' => 'save_image_info',
            'uses' => 'DocumentsController@doSaveInfo'
        )
    );

    Route::post(
        'documents/change_multiple_activity', array(
            'as' => 'change_multiple_activity_documents',
            'uses' => 'DocumentsController@doChangeActivity'
        )
    );

    Route::post(
        'documents/load_more', array(
            'as' => 'load_more_documents',
            'uses' => 'DocumentsController@doLoadMoreEndless'
        )
    );

    Route::post(
        'documents/upload', array(
            'as' => 'upload_image',
            'uses' => 'DocumentsController@doUpload'
        )
    );

    Route::post(
        'documents/replace_single', array(
            'as' => 'replace_single_image',
            'uses' => 'DocumentsController@doReplaceSingle'
        )
    );

    Route::post(
        'documents/update_new_size', array(
            'as' => 'update_new_size_documents',
            'uses' => 'DocumentsController@doUpdateNewSize'
        )
    );

}
