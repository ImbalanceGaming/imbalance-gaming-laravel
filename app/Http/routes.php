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
    Route::resource('login', 'Auth\AuthenticateController@authenticate', array('only' => array('store')));
    Route::resource('register', 'Auth\AuthenticateController@register', array('only' => array('store')));
    Route::resource('activate', 'Auth\AuthenticateController@validateEmail', array('only' => array('store')));
    Route::resource('loginUser', 'Auth\AuthenticateController@getAuthenticatedUser', array('only' => array('index')));
    Route::resource('modules', 'Modules\ModuleController', array('only' => array('index')));
});

//Route::group(array('prefix' => 'api', 'middleware' => 'jwt.auth'), function() {
//    Route::resource('users', 'Users\UserController', array('only' => array('index', 'show', 'store')));
//    Route::resource('userDetail', 'Users\UserDetailController', array('only' => array('index', 'show')));
//    Route::resource('user.userDetail', 'Users\UserDetailController@getUsersDetails', array('only' => array('index', 'show')));
//});

Route::group(array('prefix' => 'api', 'middleware' => 'jwt.auth'), function() {
    Route::resource('users', 'Users\UserController', array('only' => array('index', 'show', 'update')));
    Route::resource('usersWithDetails', 'Users\UserController@usersWithDetails', array('only' => array('index')));
    Route::resource('userDetail', 'Users\UserDetailController', array('only' => array('index', 'show')));
    Route::resource('user.userDetail', 'Users\UserDetailController@getUsersDetails', array('only' => array('index', 'show')));
});
