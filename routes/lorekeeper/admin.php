<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for users with powers.
|
*/

Route::get('/', 'HomeController@getIndex');

Route::group(['prefix' => 'users', 'namespace' => 'Users'], function() {

    # USER LIST
    Route::group(['middleware' => 'power:edit_user_info'], function() {
        Route::get('/', 'UserController@getIndex');
        
        Route::get('edit/{name}', 'UserController@getUser');
    });

    # RANKS
    Route::group(['middleware' => 'admin'], function() {
        Route::get('ranks', 'RankController@getIndex');
        Route::get('ranks/create', 'RankController@getCreateRank');
        Route::get('ranks/edit/{id}', 'RankController@getEditRank');
        Route::get('ranks/delete/{id}', 'RankController@getDeleteRank');
        Route::post('ranks/create', 'RankController@postCreateEditRank');
        Route::post('ranks/edit/{id?}', 'RankController@postCreateEditRank');
        Route::post('ranks/delete/{id}', 'RankController@postDeleteRank');
        Route::post('ranks/sort', 'RankController@postSortRanks');
    });
});