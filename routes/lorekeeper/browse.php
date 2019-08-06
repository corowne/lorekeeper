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
    Widgets
**************************************************************************************************/

Route::get('items/{id}', 'Users\InventoryController@getStack');

/**************************************************************************************************
    News
**************************************************************************************************/
# PROFILES
Route::group(['prefix' => 'news'], function() {
    Route::get('/', 'NewsController@getIndex');
    Route::get('{id}.{slug?}', 'NewsController@getNews');
    Route::get('{id}.', 'NewsController@getNews');
});

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
    
    Route::get('{name}/currency-logs', 'UserController@getUserCurrencyLogs');
    Route::get('{name}/item-logs', 'UserController@getUserItemLogs');
    Route::get('{name}/ownership', 'UserController@getUserOwnershipLogs');
    Route::get('{name}/submissions', 'UserController@getUserSubmissions');
});

/**************************************************************************************************
    Characters
**************************************************************************************************/
Route::get('/masterlist', 'BrowseController@getCharacters');
Route::group(['prefix' => 'character', 'namespace' => 'Characters'], function() {
    Route::get('{slug}', 'CharacterController@getCharacter');
    Route::get('{slug}/profile', 'CharacterController@getCharacterProfile');
    Route::get('{slug}/bank', 'CharacterController@getCharacterBank');
    Route::get('{slug}/images', 'CharacterController@getCharacterImages');
    
    Route::get('{slug}/currency-logs', 'CharacterController@getCharacterCurrencyLogs');
    Route::get('{slug}/ownership', 'CharacterController@getCharacterOwnershipLogs');
    Route::get('{slug}/change-log', 'CharacterController@getCharacterLogs');
    Route::get('{slug}/submissions', 'CharacterController@getCharacterSubmissions');

    Route::get('{slug}/profile/edit', 'CharacterController@getEditCharacterProfile');
    Route::post('{slug}/profile/edit', 'CharacterController@postEditCharacterProfile');
    
    Route::get('{slug}/transfer', 'CharacterController@getTransfer');
    Route::post('{slug}/transfer', 'CharacterController@postTransfer');
    Route::post('{slug}/transfer/{id}/cancel', 'CharacterController@postCancelTransfer');
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
    Route::get('prompt-categories', 'WorldController@getPromptCategories');
    Route::get('prompts', 'WorldController@getPrompts');
    Route::get('character-categories', 'WorldController@getCharacterCategories');
});

/**************************************************************************************************
    Site Pages
**************************************************************************************************/
Route::get('info/{key}', 'PageController@getPage');

/**************************************************************************************************
    Raffles
**************************************************************************************************/
Route::group(['prefix' => 'raffles'], function () {
    Route::get('/', 'RaffleController@getRaffleIndex');
    Route::get('view/{id}', 'RaffleController@getRaffleTickets');
});

/**************************************************************************************************
    Submissions
**************************************************************************************************/
Route::group(['prefix' => 'submissions', 'namespace' => 'Users'], function() {
    Route::get('view/{id}', 'SubmissionController@getSubmission');
});