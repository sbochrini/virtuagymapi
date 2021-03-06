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
Route::get('/', function () {
    return view('welcome');
});

Route::resource('api/plans','PlansController');
//Route::post('api/plans','PlansController@store');
Route::get('api/addplanform','PlansController@addplanform');
Route::get('api/addexerisedropdown','PlansController@addexerisedropdown');

Route::resource('api/users','UsersController');
//Route::post('api/users/store','PlansController@store');
Route::get('api/addusersform','UsersController@addusersform');
