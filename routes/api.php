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


/*Route::get('administrators/active','AdministratorController@active');
Route::get('administrators/principalCoordinator','AdministratorController@principal');
Route::resource('administrators','AdministratorController');

Route::get('teachers/active','TeacherController@active');
Route::resource('teachers','TeacherController');

Route::get('students/active','StudentController@active');
Route::resource('students','StudentController');

//Route::resource('schoolPrograms','SchoolProgramController'); // change to study program

Route::resource('subjects','SubjectController');

Route::middleware('jwt.auth')->resource('users','UserController');// No use

Route::get('schoolPeriods/current','SchoolPeriodController@current');
Route::get('schoolPeriods/subjectsTaught','SchoolPeriodController@subjectTaughtSchoolPeriod');
Route::resource('schoolPeriods','SchoolPeriodController');


Route::get('teacherInscription/enrolledStudent','InscriptionController@enrolledStudentsInSchoolPeriod');
Route::post('teacherInscription/loadNotes','InscriptionController@loadNotes');

Route::get('studentInscription/availableSubjects','InscriptionController@studentAvailableSubjects');
Route::get('studentInscription/currentEnrolledSubjects','InscriptionController@currentEnrolledSubjects');
Route::post('studentInscription','InscriptionController@addStudentInscription');
Route::post('studentInscription/withdrawSubjects','InscriptionController@withdrawSubjects');

Route::get('inscriptions/schoolPeriod/{schoolPeriodId}','InscriptionController@inscriptionBySchoolPeriod');
Route::get('inscriptions/availableSubjects','InscriptionController@availableSubjects');
Route::resource('inscriptions','InscriptionController');

Route::get('constance/study','ConstanceController@constanceOfStudy');
Route::get('constance/academicLoad','ConstanceController@academicLoad');
Route::get('constance/studentHistorical','ConstanceController@studentHistorical');
Route::get('constance/studentHistoricalDownload','ConstanceController@studentHistoricalAllData');
Route::get('constance/teacherHistorical','ConstanceController@teacherHistorical');
Route::get('constance/workTeacher','ConstanceController@constanceOfWorkTeacher');
Route::get('constance/workAdministrator','ConstanceController@constanceOfWorkAdministrator');

Route::get('constance/inscription','ConstanceController@inscriptionConstance');

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('payload', 'AuthController@payload');
});

Route::group(['middleware' => ['jwt.auth']], function() {
    //AÑADE AQUI LAS RUTAS QUE QUIERAS PROTEGER CON JWT
});

Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');
Route::post('refresh', 'AuthController@refresh');
Route::post('me', 'AuthController@me');
Route::post('payload', 'AuthController@payload');

Route::post('changePassword', 'UserController@changePassword');
Route::post('updateUser', 'UserController@changeUserData');

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.email');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.request');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');

//Route::name('print')->get('/imprimir', 'GeneradorController@imprimir');

*/

//Administrator
Route::middleware('jwt.auth','role:A')->get('administrators/active','AdministratorController@active');
Route::middleware('jwt.auth')->get('administrators/principalCoordinator','AdministratorController@principal');
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

//Subjects
Route::middleware('jwt.auth','role:A')->get('subjects','SubjectController@index');
Route::middleware('jwt.auth','role:A')->post('subjects','SubjectController@store');
Route::middleware('jwt.auth','role:A')->get('subjects/{id}','SubjectController@show');
Route::middleware('jwt.auth','role:A')->put('subjects/{id}','SubjectController@update');
Route::middleware('jwt.auth','role:A')->delete('subjects/{id}','SubjectController@destroy');

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
Route::middleware(\'jwt.auth','role:A,S')->get('constance/academicLoad','ConstanceController@academicLoad');
Route::middleware('jwt.auth','role:A,S')->get('constance/studentHistorical','ConstanceController@studentHistorical');
Route::middleware('jwt.auth','role:A,S')->get('constance/studentHistoricalData','ConstanceController@studentHistoricalData');
Route::middleware('jwt.auth','role:A,T')->get('constance/workTeacher','ConstanceController@constanceOfWorkTeacher');
Route::get('constance/workAdministrator','ConstanceController@constanceOfWorkAdministrator');
Route::middleware('jwt.auth','role:A,S')->get('constance/inscription','ConstanceController@inscriptionConstance');

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

