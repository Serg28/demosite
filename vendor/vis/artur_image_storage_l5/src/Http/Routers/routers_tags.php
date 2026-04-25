<?php
Route::any(
    'tags', array(
        'as' => 'tags_all',
        'uses' => 'TagsController@fetchIndex'
    )
);

if (Request::ajax()) {

    Route::post(
        'tags/delete', array(
            'as' => 'delete_tag',
            'uses' => 'TagsController@doDelete'
        )
    );

    Route::post(
        'tags/get_form', array(
            'as' => 'get_tags_edit_form',
            'uses' => 'TagsController@getForm'
        )
    );


    Route::post(
        'tags/save_info', array(
            'as' => 'save_tag_info',
            'uses' => 'TagsController@doSaveInfo'
        )
    );

    Route::post(
        'tags/relate_to_tags/{type}', array(
            'as' => 'add_images_to_tags',
            'uses' => 'TagsController@doRelateToTags'
        )
    );

}
