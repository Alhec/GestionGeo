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

//Route::middleware('jwt.auth','role:A')->resource('postgraduates','PostgraduateController');
Route::resource('postgraduates','PostgraduateController');
//Route::middleware('jwt.auth')->resource('subjects','SubjectController');
Route::resource('subjects','SubjectController');
//Route::middleware('jwt.auth')->resource('users','UserController');
Route::get('administrators/active','AdministratorController@active');
Route::resource('administrators','AdministratorController');
Route::get('teachers/active','TeacherController@active');
Route::resource('teachers','TeacherController');
Route::get('students/active','StudentController@active');
Route::resource('students','StudentController');
Route::get('schoolPeriods/current','SchoolPeriodController@current');
Route::resource('schoolPeriods','SchoolPeriodController');
//Route::resource('subjectInscription','SubjectInscriptionController');
//Route::resource('schoolPeriodInscription','SchoolPeriodInscriptionController');
Route::get('inscriptions/schoolPeriod/{schoolPeriodId}','InscriptionController@inscriptionBySchoolPeriod');
Route::get('inscriptions/availableSubjects','InscriptionController@availableSubjects');
Route::get('studentInscription/availableSubjects','InscriptionController@studentAvailableSubjects');
Route::get('studentInscription/currentEnrolledSubjects','InscriptionController@currentEnrolledSubjects');
Route::post('studentInscription','InscriptionController@addStudentInscription');
Route::post('studentInscription/withdrawSubjects','InscriptionController@withdrawSubjects');
Route::resource('inscriptions','InscriptionController');
//Route::post('login', 'AuthController@login');
//Route::get('log', 'AuthController@getToken');
/*Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('payload', 'AuthController@payload');
});*/
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('refresh', 'AuthController@refresh');
Route::post('me', 'AuthController@me');
Route::post('payload', 'AuthController@payload');
Route::group(['middleware' => ['jwt.auth']], function() {
    /*AÑADE AQUI LAS RUTAS QUE QUIERAS PROTEGER CON JWT*/
});
//Route::post('password/email', 'PasswordController@postEmail');
//Route::post('password/reset', 'PasswordController@postReset');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.email');;
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.request');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
