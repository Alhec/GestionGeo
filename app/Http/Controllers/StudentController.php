<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\StudentService;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return UserService::getUsers($request,'S');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return StudentService::addNewStudent($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        return UserService::getUserById($request,$id,'S');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return StudentService::updateStudent($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        return UserService::deleteUser($request,$id,'S');
    }

    public function active(Request $request)
    {
        return UserService::activeUsers($request,'S');
    }

    public function addStudentToUser($id,Request $request)
    {
        return StudentService::addStudentContinue($request,$id);
    }

    public function deleteStudent($id,Request $request)
    {
        $studentId = $request->input('student_id');
        return StudentService::deleteStudent($id,$studentId,$request);
    }
}
