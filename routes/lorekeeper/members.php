<?php

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
|
| Routes for logged in users with a linked dA account.
|
*/


Route::group(['prefix' => 'user', 'namespace' => 'Users'], function() {
    Route::get('{name}', 'UserController@getUser');
});

Route::group(['prefix' => 'account', 'namespace' => 'Users'], function() {
    Route::get('settings', 'AccountController@getSettings');
});