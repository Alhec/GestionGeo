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

Route::resource('postgraduates','PostgraduateController');
Route::resource('subjects','SubjectController');
//Route::middleware('jwt.auth')->resource('users','UserController');
Route::resource('administrators','AdministratorController');
Route::resource('teachers','TeacherController');
Route::resource('students','StudentController');
Route::resource('schoolPeriods','SchoolPeriodController');
Route::resource('subjectInscription','SubjectInscriptionController');
Route::resource('schoolPeriodInscription','SchoolPeriodInscriptionController');
Route::post('login', 'AuthController@login');
