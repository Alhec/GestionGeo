
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

Route::middleware('app.auth','jwt.auth')->group(function (){
    Route::post('me', 'AuthController@me');
    Route::post('payload', 'AuthController@payload');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('logout', 'AuthController@logout');
});

// Password Reset
Route::middleware('app.auth')->prefix('password')->group(function (){
    Route::post('/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
});

//Comun Users
Route::middleware('app.auth','jwt.auth')->group(function (){
    Route::post('changePassword', 'UserController@changePassword');
    Route::post('updateUser', 'UserController@changeUserData');
});

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
Route::middleware('app.auth','jwt.auth')->prefix('teachers')->group(function (){
    Route::middleware('role:A,S')->get('/','TeacherController@index');
    Route::middleware('role:A')->get('/active','TeacherController@active');
    Route::middleware('role:A')->get('/{id}','TeacherController@show');
    Route::middleware('role:A')->post('/','TeacherController@store');
    Route::middleware('role:A')->put('/{id}','TeacherController@update');
    Route::middleware('role:A')->delete('/{id}','TeacherController@destroy');
});

//SchoolProgram
Route::middleware('app.auth','jwt.auth','role:A')->prefix('schoolPrograms')->group(function (){
    Route::get('/','SchoolProgramController@index');
    Route::get('/{id}','SchoolProgramController@show');
    Route::post('/','SchoolProgramController@store');
    Route::put('/{id}','SchoolProgramController@update');
    Route::delete('/{id}','SchoolProgramController@destroy');
});

//Student
Route::middleware('app.auth','jwt.auth','role:A')->prefix('students')->group(function (){
    Route::get('/','StudentController@index');
    Route::get('/active','StudentController@active');
    Route::get('/{id}','StudentController@show');
    Route::post('/','StudentController@store');
    Route::put('/{id}','StudentController@update');
    Route::put('/continue/{id}','StudentController@addStudentToUser');
    Route::delete('/delete/{id}','StudentController@deleteStudent');
    Route::delete('/{id}','StudentController@destroy');
});
Route::middleware('app.auth','jwt.auth','role:A')->get('warningStudents','StudentController@warningStudent');

//Subject
Route::middleware('app.auth','jwt.auth','role:A')->prefix('subjects')->group(function (){
    Route::get('/','SubjectController@index');
    Route::get('/{id}','SubjectController@show');
    Route::post('/','SubjectController@store');
    Route::put('/{id}','SubjectController@update');
    Route::delete('/{id}','SubjectController@destroy');
});
Route::middleware('app.auth','jwt.auth','role:A')->group(function (){
    Route::get('subjectsBySchoolProgram/{id}','SubjectController@getBySchoolProgram');
    Route::get('subjectsWithoutFinalWorks','SubjectController@getSubjectsWithoutFinalWorks');
});

//SchoolPeriod
Route::middleware('app.auth','jwt.auth')->prefix('schoolPeriods')->group(function (){
    Route::middleware('role:A,S,T')->get('/current','SchoolPeriodController@current');
    Route::middleware('role:A,T')->get('/subjectsTaught','SchoolPeriodController@subjectTaughtSchoolPeriod');
    Route::middleware('role:A')->group(function (){
        Route::get('/','SchoolPeriodController@index');
        Route::get('/{id}','SchoolPeriodController@show');
        Route::post('/','SchoolPeriodController@store');
        Route::put('/{id}','SchoolPeriodController@update');
        Route::delete('/{id}','SchoolPeriodController@destroy');
    });
});


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
Route::middleware('app.auth','jwt.auth')->prefix('constance')->group(function (){
    Route::middleware('role:A')->get('/workAdministrator','ConstanceController@constanceOfWorkAdministrator');
    Route::middleware('role:A,T')->get('/workTeacher','ConstanceController@constanceOfWorkTeacher');
    Route::middleware('role:A,S')->group(function (){
        Route::get('/study','ConstanceController@constanceOfStudy');
        Route::get('/academicLoad','ConstanceController@academicLoad');
        Route::get('/studentHistorical','ConstanceController@studentHistorical');
        Route::get('/inscription','ConstanceController@inscriptionConstance');
    });
});

//Annual Report
Route::middleware('app.auth','jwt.auth','role:A')->get('annualReport','AnnualReportController@exportAnnualReport');

//Test
Route::get('test', function () {
    return 'GAAPFC';
});
Route::get('test/{id}','SchoolProgramController@show');

