<?php

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
|
| Routes for logged in users with a linked dA account.
|
*/

/**************************************************************************************************
    Users
**************************************************************************************************/

Route::group(['prefix' => 'notifications', 'namespace' => 'Users'], function() {
    Route::get('/', 'AccountController@getNotifications');
    Route::get('delete/{id}', 'AccountController@getDeleteNotification');
    Route::post('clear', 'AccountController@postClearNotifications');
    Route::post('clear/{type}', 'AccountController@postClearNotifications');
});

Route::group(['prefix' => 'account', 'namespace' => 'Users'], function() {
    Route::get('settings', 'AccountController@getSettings');
    Route::post('profile', 'AccountController@postProfile');
    Route::post('password', 'AccountController@postPassword');
    Route::post('email', 'AccountController@postEmail');
    Route::post('avatar', 'AccountController@postAvatar');
    Route::get('aliases', 'AccountController@getAliases');
    Route::get('make-primary/{id}', 'AccountController@getMakePrimary');
    Route::post('make-primary/{id}', 'AccountController@postMakePrimary');
    Route::get('hide-alias/{id}', 'AccountController@getHideAlias');
    Route::post('hide-alias/{id}', 'AccountController@postHideAlias');
    Route::get('remove-alias/{id}', 'AccountController@getRemoveAlias');
    Route::post('remove-alias/{id}', 'AccountController@postRemoveAlias');
    Route::post('dob', 'AccountController@postBirthday');

    Route::get('bookmarks', 'BookmarkController@getBookmarks');
    Route::get('bookmarks/create', 'BookmarkController@getCreateBookmark');
    Route::get('bookmarks/edit/{id}', 'BookmarkController@getEditBookmark');
    Route::post('bookmarks/create', 'BookmarkController@postCreateEditBookmark');
    Route::post('bookmarks/edit/{id}', 'BookmarkController@postCreateEditBookmark');
    Route::get('bookmarks/delete/{id}', 'BookmarkController@getDeleteBookmark');
    Route::post('bookmarks/delete/{id}', 'BookmarkController@postDeleteBookmark');
});

Route::group(['prefix' => 'inventory', 'namespace' => 'Users'], function() {
    Route::get('/', 'InventoryController@getIndex');
    Route::post('edit', 'InventoryController@postEdit');
    Route::get('account-search', 'InventoryController@getAccountSearch');

    Route::get('selector', 'InventoryController@getSelector');

    Route::get('quickstock', 'InventoryController@getQuickstock');
    Route::post('quickstock-items', 'InventoryController@postQuickstock');
});

Route::group(['prefix' => 'characters', 'namespace' => 'Users'], function() {
    Route::get('/', 'CharacterController@getIndex');
    Route::post('sort', 'CharacterController@postSortCharacters');

    Route::get('transfers/{type}', 'CharacterController@getTransfers');
    Route::post('transfer/act/{id}', 'CharacterController@postHandleTransfer');

    Route::get('myos', 'CharacterController@getMyos');
});

Route::group(['prefix' => 'bank', 'namespace' => 'Users'], function() {
    Route::get('/', 'BankController@getIndex');
    Route::post('transfer', 'BankController@postTransfer');
});

Route::group(['prefix' => 'level', 'namespace' => 'Users'], function() {
    Route::get('/', 'LevelController@getIndex');
    Route::post('up', 'LevelController@postLevel');
    Route::post('transfer', 'LevelController@postTransfer');
});

Route::group(['prefix' => 'trades', 'namespace' => 'Users'], function() {
    Route::get('{status}', 'TradeController@getIndex')->where('status', 'open|pending|completed|rejected|canceled');
    Route::get('create', 'TradeController@getCreateTrade');
    Route::get('{id}/edit', 'TradeController@getEditTrade')->where('id', '[0-9]+');
    Route::post('create', 'TradeController@postCreateTrade');
    Route::post('{id}/edit', 'TradeController@postEditTrade')->where('id', '[0-9]+');
    Route::get('{id}', 'TradeController@getTrade')->where('id', '[0-9]+');

    Route::get('{id}/confirm-offer', 'TradeController@getConfirmOffer');
    Route::post('{id}/confirm-offer', 'TradeController@postConfirmOffer');
    Route::get('{id}/confirm-trade', 'TradeController@getConfirmTrade');
    Route::post('{id}/confirm-trade', 'TradeController@postConfirmTrade');
    Route::get('{id}/cancel-trade', 'TradeController@getCancelTrade');
    Route::post('{id}/cancel-trade', 'TradeController@postCancelTrade');
});

Route::group(['prefix' => 'user-shops', 'namespace' => 'Users'], function() {
    Route::get('/', 'UserShopController@getUserIndex'); 
    Route::get('create', 'UserShopController@getCreateShop');
    Route::get('edit/{id}', 'UserShopController@getEditShop');
    Route::get('delete/{id}', 'UserShopController@getDeleteShop');
    Route::post('create', 'UserShopController@postCreateEditShop');
    Route::post('edit/{id?}', 'UserShopController@postCreateEditShop');
    Route::post('stock/{id}', 'UserShopController@postEditShopStock');
    Route::post('delete/{id}', 'UserShopController@postDeleteShop');
    Route::post('sort', 'UserShopController@postSortShop');

    // misc
    Route::get('/stock-type', 'UserShopController@getShopStockType');
    Route::get('/history', 'UserShopController@getPurchaseHistory');
    Route::get('item-search', 'UserShopController@getItemSearch');

    Route::get('sales/{id}', 'UserShopController@getShopHistory');

    Route::post('quickstock/{id}', 'UserShopController@postQuickstockStock');
});

Route::group(['prefix' => 'user-shops',], function() {
    Route::get('/shop-index', 'UserShopController@getIndex'); 
    Route::get('/shop/{id}', 'UserShopController@getShop'); 
    Route::post('/shop/buy', 'UserShopController@postBuy');
    Route::get('{id}/{stockId}', 'UserShopController@getShopStock')->where(['id' => '[0-9]+', 'stockId' => '[0-9]+']);
});

Route::group(['prefix' => 'crafting', 'namespace' => 'Users'], function() {
    Route::get('/', 'CraftingController@getIndex');
    Route::get('craft/{id}', 'CraftingController@getCraftRecipe');
    Route::post('craft/{id}', 'CraftingController@postCraftRecipe');
});

/**************************************************************************************************
    Characters
**************************************************************************************************/
Route::group(['prefix' => 'character', 'namespace' => 'Characters'], function() {
    Route::get('{slug}/profile/edit', 'CharacterController@getEditCharacterProfile');
    Route::post('{slug}/profile/edit', 'CharacterController@postEditCharacterProfile');

    Route::post('{slug}/inventory/edit', 'CharacterController@postInventoryEdit');

    Route::post('{slug}/bank/transfer', 'CharacterController@postCurrencyTransfer');
    Route::get('{slug}/transfer', 'CharacterController@getTransfer');
    Route::post('{slug}/transfer', 'CharacterController@postTransfer');
    Route::post('{slug}/transfer/{id}/cancel', 'CharacterController@postCancelTransfer');

    Route::post('{slug}/approval', 'CharacterController@postCharacterApproval');
    Route::get('{slug}/approval', 'CharacterController@getCharacterApproval');
    Route::get('{slug}/level-area', 'LevelController@getIndex');
    Route::get('{slug}/stats-area', 'LevelController@getStatsIndex');
    Route::post('{slug}/level-area/up', 'LevelController@postLevel');
    Route::post('{slug}/stats-area/{id}', 'LevelController@postStat');
    Route::post('{slug}/stats-area/admin/{id}', 'LevelController@postAdminStat');
    Route::post('{slug}/stats-area/edit/{id}', 'LevelController@postEditStat');
    # EXP
    Route::post('{slug}/level-area/exp-grant', 'LevelController@postExpGrant');
    Route::post('{slug}/level-area/stat-grant', 'LevelController@postStatGrant');
});
Route::group(['prefix' => 'myo', 'namespace' => 'Characters'], function() {
    Route::get('{id}/profile/edit', 'MyoController@getEditCharacterProfile');
    Route::post('{id}/profile/edit', 'MyoController@postEditCharacterProfile');

    Route::get('{id}/transfer', 'MyoController@getTransfer');
    Route::post('{id}/transfer', 'MyoController@postTransfer');
    Route::post('{id}/transfer/{id2}/cancel', 'MyoController@postCancelTransfer');

    Route::post('{id}/approval', 'MyoController@postCharacterApproval');
    Route::get('{id}/approval', 'MyoController@getCharacterApproval');
});

Route::group(['prefix' => 'level', 'namespace' => 'Users'], function() {
    Route::get('/', 'LevelController@getIndex');

});



/**************************************************************************************************
    Submissions
**************************************************************************************************/

Route::group(['prefix' => 'gallery'], function() {
    Route::get('submissions/{type}', 'GalleryController@getUserSubmissions')->where('type', 'pending|accepted|rejected');

    Route::post('favorite/{id}', 'GalleryController@postFavoriteSubmission');

    Route::get('submit/{id}', 'GalleryController@getNewGallerySubmission');
    Route::get('submit/character/{slug}', 'GalleryController@getCharacterInfo');
    Route::get('edit/{id}', 'GalleryController@getEditGallerySubmission');
    Route::get('queue/{id}', 'GalleryController@getSubmissionLog');
    Route::post('submit', 'GalleryController@postCreateEditGallerySubmission');
    Route::post('edit/{id}', 'GalleryController@postCreateEditGallerySubmission');

    Route::post('collaborator/{id}', 'GalleryController@postEditCollaborator');

    Route::get('archive/{id}', 'GalleryController@getArchiveSubmission');
    Route::post('archive/{id}', 'GalleryController@postArchiveSubmission');
});

Route::group(['prefix' => 'submissions', 'namespace' => 'Users'], function() {
    Route::get('/', 'SubmissionController@getIndex');
    Route::get('new', 'SubmissionController@getNewSubmission');
    Route::get('new/character/{slug}', 'SubmissionController@getCharacterInfo');
    Route::get('new/prompt/{id}', 'SubmissionController@getPromptInfo');
    Route::post('new', 'SubmissionController@postNewSubmission');
});

Route::group(['prefix' => 'claims', 'namespace' => 'Users'], function() {
    Route::get('/', 'SubmissionController@getClaimsIndex');
    Route::get('new', 'SubmissionController@getNewClaim');
    Route::post('new', 'SubmissionController@postNewClaim');
});

Route::group(['prefix' => 'reports', 'namespace' => 'Users'], function() {
    Route::get('/', 'ReportController@getReportsIndex');
    Route::get('new', 'ReportController@getNewReport');
    Route::post('new', 'ReportController@postNewReport');
    Route::get('view/{id}', 'ReportController@getReport');
});

Route::group(['prefix' => 'designs', 'namespace' => 'Characters'], function() {
    Route::get('{type?}', 'DesignController@getDesignUpdateIndex')->where('type', 'pending|approved|rejected');
    Route::get('{id}', 'DesignController@getDesignUpdate');

    Route::get('{id}/comments', 'DesignController@getComments');
    Route::post('{id}/comments', 'DesignController@postComments');

    Route::get('{id}/image', 'DesignController@getImage');
    Route::post('{id}/image', 'DesignController@postImage');

    Route::get('{id}/addons', 'DesignController@getAddons');
    Route::post('{id}/addons', 'DesignController@postAddons');

    Route::get('{id}/traits', 'DesignController@getFeatures');
    Route::post('{id}/traits', 'DesignController@postFeatures');
    Route::get('traits/subtype', 'DesignController@getFeaturesSubtype');

    Route::get('{id}/confirm', 'DesignController@getConfirm');
    Route::post('{id}/submit', 'DesignController@postSubmit');

    Route::get('{id}/delete', 'DesignController@getDelete');
    Route::post('{id}/delete', 'DesignController@postDelete');
});

/**************************************************************************************************
    Encounters
**************************************************************************************************/

Route::group(['prefix' => 'encounter-areas'], function() {
    Route::get('/', 'EncounterController@getEncounterAreas');

    Route::get('{id}', 'EncounterController@exploreArea')->where('id', '[0-9]+');
    Route::post('{id}/act', 'EncounterController@postAct')->where('id', '[0-9]+');
    Route::post('select-character', 'EncounterController@postSelectCharacter');
});

/**************************************************************************************************
    Shops
**************************************************************************************************/

Route::group(['prefix' => 'shops'], function() {
    Route::post('buy', 'ShopController@postBuy');
    Route::get('history', 'ShopController@getPurchaseHistory');
});

/**************************************************************************************************
    Comments
**************************************************************************************************/
Route::group(['prefix' => 'comments', 'namespace' => 'Comments'], function() {
    Route::post('/', 'CommentController@store')->name('comments.store');
    Route::delete('/{comment}', 'CommentController@destroy')->name('comments.destroy');
    Route::put('/{comment}', 'CommentController@update')->name('comments.update');
    Route::post('/{comment}', 'CommentController@reply')->name('comments.reply');
    Route::post('/{id}/feature', 'CommentController@feature')->name('comments.feature');
});

