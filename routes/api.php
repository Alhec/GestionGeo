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

//Authentication
Route::post('login', 'AuthController@login');
Route::middleware('jwt.auth')->post('logout', 'AuthController@logout');
Route::middleware('jwt.auth')->post('refresh', 'AuthController@refresh');
Route::middleware('jwt.auth')->post('me', 'AuthController@me');
Route::middleware('jwt.auth')->post('payload', 'AuthController@payload');

//Comun Users
Route::middleware('jwt.auth')->post('changePassword', 'UserController@changePassword');
Route::middleware('jwt.auth')->post('updateUser', 'UserController@changeUserData');

// Password Reset
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');

//Administrator
Route::middleware('jwt.auth','role:A')->get('administrators/active','AdministratorController@active');
Route::middleware('jwt.auth','role:A')->get('administrators/principalCoordinator','AdministratorController@principal');
Route::middleware('jwt.auth','role:A')->get('administrators','AdministratorController@index');
Route::middleware('jwt.auth','role:A')->post('administrators','AdministratorController@store');
Route::middleware('jwt.auth','role:A')->get('administrators/{id}','AdministratorController@show');
Route::middleware('jwt.auth','role:A')->put('administrators/{id}','AdministratorController@update');
Route::middleware('jwt.auth','role:A')->delete('administrators/{id}','AdministratorController@destroy');

//Teacher
Route::middleware('jwt.auth','role:A')->get('teachers/active','TeacherController@active');
Route::middleware('jwt.auth','role:A')->get('teachers','TeacherController@index');
Route::middleware('jwt.auth','role:A')->post('teachers','TeacherController@store');
Route::middleware('jwt.auth','role:A')->get('teachers/{id}','TeacherController@show');
Route::middleware('jwt.auth','role:A')->put('teachers/{id}','TeacherController@update');
Route::middleware('jwt.auth','role:A')->delete('teachers/{id}','TeacherController@destroy');

//Student
Route::middleware('jwt.auth','role:A')->get('students/active','StudentController@active');
Route::middleware('jwt.auth','role:A')->get('students','StudentController@index');
Route::middleware('jwt.auth','role:A')->post('students','StudentController@store');
Route::middleware('jwt.auth','role:A')->get('students/{id}','StudentController@show');
Route::middleware('jwt.auth','role:A')->put('students/{id}','StudentController@update');
Route::middleware('jwt.auth','role:A')->delete('students/{id}','StudentController@destroy');
Route::middleware('jwt.auth','role:A')->put('students/continue/{id}','StudentController@addStudentToUser');
Route::middleware('jwt.auth','role:A')->delete('students/delete/{id}','StudentController@deleteStudent');
Route::middleware('jwt.auth','role:A')->get('warningStudents','StudentController@warningStudent');

//SchoolProgram
Route::middleware('jwt.auth','role:A')->get('schoolPrograms','SchoolProgramController@index');
Route::middleware('jwt.auth','role:A')->post('schoolPrograms','SchoolProgramController@store');
Route::middleware('jwt.auth','role:A')->get('schoolPrograms/{id}','SchoolProgramController@show');
Route::middleware('jwt.auth','role:A')->put('schoolPrograms/{id}','SchoolProgramController@update');
Route::middleware('jwt.auth','role:A')->delete('schoolPrograms/{id}','SchoolProgramController@destroy');

//Subject
Route::middleware('jwt.auth','role:A')->get('subjects','SubjectController@index');
Route::middleware('jwt.auth','role:A')->post('subjects','SubjectController@store');
Route::middleware('jwt.auth','role:A')->get('subjects/{id}','SubjectController@show');
Route::middleware('jwt.auth','role:A')->put('subjects/{id}','SubjectController@update');
Route::middleware('jwt.auth','role:A')->delete('subjects/{id}','SubjectController@destroy');
Route::middleware('jwt.auth','role:A')->get('subjectsBySchoolProgram/{id}','SubjectController@getBySchoolProgram');
Route::middleware('jwt.auth','role:A')->get('subjectsWithoutFinalWorks','SubjectController@getSubjectsWithoutFinalWorks');

//SchoolPeriod
Route::middleware('jwt.auth','role:A,S,T')->get('schoolPeriods/current','SchoolPeriodController@current');
Route::middleware('jwt.auth','role:A,T')->get('schoolPeriods/subjectsTaught','SchoolPeriodController@subjectTaughtSchoolPeriod');
Route::middleware('jwt.auth','role:A')->get('schoolPeriods','SchoolPeriodController@index');
Route::middleware('jwt.auth','role:A')->post('schoolPeriods','SchoolPeriodController@store');
Route::middleware('jwt.auth','role:A')->get('schoolPeriods/{id}','SchoolPeriodController@show');
Route::middleware('jwt.auth','role:A')->put('schoolPeriods/{id}','SchoolPeriodController@update');
Route::middleware('jwt.auth','role:A')->delete('schoolPeriods/{id}','SchoolPeriodController@destroy');

//Inscription
Route::middleware('jwt.auth','role:A,T')->get('teacherInscription/enrolledStudent','InscriptionController@enrolledStudentsInSchoolPeriod');
Route::middleware('jwt.auth','role:T')->post('teacherInscription/loadNotes','InscriptionController@loadNotes');

Route::middleware('jwt.auth','role:S')->get('studentInscription/availableSubjects','InscriptionController@studentAvailableSubjects');
Route::middleware('jwt.auth','role:S')->get('studentInscription/currentEnrolledSubjects','InscriptionController@currentEnrolledSubjects');
Route::middleware('jwt.auth','role:S')->post('studentInscription','InscriptionController@addStudentInscription');
Route::middleware('jwt.auth','role:S')->post('studentInscription/withdrawSubjects','InscriptionController@withdrawSubjects');

Route::middleware('jwt.auth','role:A')->get('inscriptions/schoolPeriod/{schoolPeriodId}','InscriptionController@inscriptionBySchoolPeriod');
Route::middleware('jwt.auth','role:A')->get('inscriptions/availableSubjects','InscriptionController@availableSubjects');
Route::middleware('jwt.auth','role:A')->get('inscriptions','InscriptionController@index');
Route::middleware('jwt.auth','role:A')->post('inscriptions','InscriptionController@store');
Route::middleware('jwt.auth','role:A')->get('inscriptions/{id}','InscriptionController@show');
Route::middleware('jwt.auth','role:A')->put('inscriptions/{id}','InscriptionController@update');
Route::middleware('jwt.auth','role:A')->delete('inscriptions/{id}','InscriptionController@destroy');

//Constance
Route::middleware('jwt.auth','role:A,S')->get('constance/study','ConstanceController@constanceOfStudy');
Route::middleware('jwt.auth','role:A,S')->get('constance/academicLoad','ConstanceController@academicLoad');
Route::middleware('jwt.auth','role:A,S')->get('constance/studentHistorical','ConstanceController@studentHistorical');
Route::middleware('jwt.auth','role:A,S')->get('constance/studentHistoricalData','ConstanceController@studentHistoricalData');
Route::middleware('jwt.auth','role:A,T')->get('constance/workTeacher','ConstanceController@constanceOfWorkTeacher');
Route::get('constance/workAdministrator','ConstanceController@constanceOfWorkAdministrator');
Route::middleware('jwt.auth','role:A,S')->get('constance/inscription','ConstanceController@inscriptionConstance');



