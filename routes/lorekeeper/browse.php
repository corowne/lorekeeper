<?php

/*
|--------------------------------------------------------------------------
| Browse Routes
|--------------------------------------------------------------------------
|
| Routes for pages that don't require being logged in to view,
| specifically the information pages.
|
*/

/**************************************************************************************************
    Users
**************************************************************************************************/
Route::get('/users', 'BrowseController@getUsers');

# PROFILES
Route::group(['prefix' => 'user', 'namespace' => 'Users'], function() {
    Route::get('{name}', 'UserController@getUser');
    Route::get('{name}/characters', 'UserController@getUserCharacters');
    Route::get('{name}/inventory', 'UserController@getUserInventory');
    Route::get('{name}/bank', 'UserController@getUserBank');
});


/**************************************************************************************************
    World
**************************************************************************************************/

Route::group(['prefix' => 'world'], function() {
    Route::get('/', 'WorldController@getIndex');
    
    Route::get('currencies', 'WorldController@getCurrencies');
    Route::get('rarities', 'WorldController@getRarities');
    Route::get('species', 'WorldController@getSpecieses');
    Route::get('item-categories', 'WorldController@getItemCategories');
    Route::get('items', 'WorldController@getItems');
    Route::get('trait-categories', 'WorldController@getFeatureCategories');
    Route::get('traits', 'WorldController@getFeatures');
});