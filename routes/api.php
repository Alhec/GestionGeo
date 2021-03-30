
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
Route::middleware('app.auth')->post('login', 'AuthController@login');
Route::middleware('app.auth','jwt.auth')->post('me', 'AuthController@me');
Route::middleware('app.auth','jwt.auth')->post('payload', 'AuthController@payload');
Route::middleware('app.auth','jwt.auth')->post('refresh', 'AuthController@refresh');
Route::middleware('app.auth','jwt.auth')->post('logout', 'AuthController@logout');

// Password Reset
Route::middleware('app.auth')->prefix('password')->group(function (){
    Route::post('/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
});

//Comun Users
Route::middleware('app.auth','jwt.auth')->post('changePassword', 'UserController@changePassword');
Route::middleware('app.auth','jwt.auth')->post('updateUser', 'UserController@changeUserData');

//Administrator
Route::middleware('app.auth','jwt.auth','role:A')->prefix('administrators')->group(function (){
    Route::get('/','AdministratorController@index');
    Route::get('/active','AdministratorController@active');
    Route::get('/principalCoordinator','AdministratorController@principal');
    Route::get('/{id}','AdministratorController@show');
    Route::post('/','AdministratorController@store');
    Route::put('/{id}','AdministratorController@update');
    Route::delete('/{id}','AdministratorController@destroy');
});

//Teacher
Route::middleware('jwt.auth','role:A')->get('teachers/active','TeacherController@active');
Route::middleware('jwt.auth','role:A,S')->get('teachers','TeacherController@index');
Route::middleware('jwt.auth','role:A')->post('teachers','TeacherController@store');
Route::middleware('jwt.auth','role:A')->get('teachers/{id}','TeacherController@show');
Route::middleware('jwt.auth','role:A')->put('teachers/{id}','TeacherController@update');
Route::middleware('jwt.auth','role:A')->delete('teachers/{id}','TeacherController@destroy');

//SchoolProgram
Route::middleware('jwt.auth','role:A')->get('schoolPrograms','SchoolProgramController@index');
Route::middleware('jwt.auth','role:A')->post('schoolPrograms','SchoolProgramController@store');
Route::middleware('jwt.auth','role:A')->get('schoolPrograms/{id}','SchoolProgramController@show');
Route::middleware('jwt.auth','role:A')->put('schoolPrograms/{id}','SchoolProgramController@update');
Route::middleware('jwt.auth','role:A')->delete('schoolPrograms/{id}','SchoolProgramController@destroy');

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
Route::middleware('jwt.auth','role:A,T')->post('teacherInscription/loadNotes','InscriptionController@loadNotes');

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
Route::middleware('jwt.auth','role:A')->delete('inscriptions/deleteFinalWork/{id}','InscriptionController@deleteFinalWork');

//Constance
Route::middleware('jwt.auth','role:A,S')->get('constance/study','ConstanceController@constanceOfStudy');
Route::middleware('jwt.auth','role:A,S')->get('constance/academicLoad','ConstanceController@academicLoad');
Route::middleware('jwt.auth','role:A,S')->get('constance/studentHistorical','ConstanceController@studentHistorical');
Route::middleware('jwt.auth','role:A,T')->get('constance/workTeacher','ConstanceController@constanceOfWorkTeacher');
Route::middleware('jwt.auth','role:A')->get('constance/workAdministrator','ConstanceController@constanceOfWorkAdministrator');
Route::middleware('jwt.auth','role:A,S')->get('constance/inscription','ConstanceController@inscriptionConstance');

//Annual Report
Route::middleware('jwt.auth','role:A')->get('annualReport','AnnualReportController@exportAnnualReport');

//Test
Route::get('test', function () {
    return 'GAAPFC';
});
Route::get('test/{id}','SchoolProgramController@show');

