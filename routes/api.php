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
Route::middleware('jwt.auth')->resource('subjects','SubjectController');
Route::middleware('jwt.auth')->resource('users','UserController');
Route::post('login', 'AuthController@login');