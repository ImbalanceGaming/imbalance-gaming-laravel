<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    View::make('index'); // will return app/views/index.php
});

//// API ROUTES ==================================
Route::group(array('prefix' => 'api'), function() {
    Route::resource('users', 'UserController', array('only' => array('index', 'show')));
    Route::resource('userDetails', 'UserDetailsController', array('only' => array('index', 'show')));
    Route::resource('user.userDetails', 'UserDetailsController@getUsersDetails', array('only' => array('index', 'show')));
});

Route::group(array('prefix' => 'api', 'middleware' => 'auth'), function() {
    Route::resource('users', 'UserController', array('only' => array('store')));
});
