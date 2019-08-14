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

Route::middleware('jwt.auth')->resource('postgraduates','PostgraduateController');
Route::resource('subjects','SubjectController');
//Route::resource('users','UserController');
Route::middleware('jwt.auth')->resource('administrators','AdministratorController');
Route::post('login', 'AuthController@login');