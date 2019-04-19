<?php

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
|
| Routes for logged in users with a linked dA account.
|
*/



Route::group(['prefix' => 'account', 'namespace' => 'Users'], function() {
    Route::get('settings', 'AccountController@getSettings');
});

Route::group(['prefix' => 'inventory', 'namespace' => 'Users'], function() {
    Route::get('inventory', 'InventoryController@getIndex');
});

Route::group(['prefix' => 'characters', 'namespace' => 'Users'], function() {
    Route::get('inventory', 'CharacterController@getIndex');
});

Route::group(['prefix' => 'bank', 'namespace' => 'Users'], function() {
    Route::get('bank', 'BankController@getIndex');
});