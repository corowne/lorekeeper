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
    
    Route::get('/auth/redirect/{driver}', 'HomeController@getAuthRedirect');
    Route::get('/auth/callback/{driver}', 'HomeController@getAuthCallback');

    # SET BIRTHDATE
    Route::get('/birthday', 'HomeController@getBirthday')->name('birthday');
    Route::post('/birthday', 'HomeController@postBirthday');

    Route::get('/blocked', 'HomeController@getBirthdayBlocked')->name('blocked');

    # BANNED
    Route::get('banned', 'Users\AccountController@getBanned');

    /**********************************************************************************************
        Routes that require having a linked dA account (also includes blocked routes when banned)
    **********************************************************************************************/
    Route::group(['middleware' => ['alias']], function() {

        require_once __DIR__.'/lorekeeper/members.php';

        /**********************************************************************************************
            Admin panel routes
        **********************************************************************************************/
        Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['staff']], function() {

            require_once __DIR__.'/lorekeeper/admin.php';

        });
    });
});
