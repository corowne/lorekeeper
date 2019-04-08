<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@getIndex')->name('home');
Route::get('login', 'Auth\LoginController@getNewReply');
Auth::routes(['verify' => true]);

# BROWSE
require_once __DIR__.'/lorekeeper/browse.php';

/**************************************************************************************************
    Routes that require login
**************************************************************************************************/
Route::group(['middleware' => ['auth', 'verified']], function() {

    # LINK DA ACCOUNT
    Route::get('/link', 'HomeController@getLink')->name('link');

    /**********************************************************************************************
        Routes that require having a linked dA account
    **********************************************************************************************/
    Route::group(['middleware' => ['alias']], function() {

        require_once __DIR__.'/lorekeeper/members.php';

        /**********************************************************************************************
            Admin panel routes
        **********************************************************************************************/
        Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function() {

            require_once __DIR__.'/lorekeeper/admin.php';

        });
    });
});
