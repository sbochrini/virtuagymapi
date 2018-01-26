<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

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
