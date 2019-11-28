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
        $organizationId = $request->header('organization_key');
        return UserService::getUsers($request,'S',$organizationId);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $organizationId = $request->header('organization_key');
        return StudentService::addNewStudent($request,$organizationId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $organizationId = $request->header('organization_key');
        return UserService::getUserById($request,$id,'S',$organizationId);
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
        $organizationId = $request->header('organization_key');
        return StudentService::updateStudent($request,$id,$organizationId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $organizationId = $request->header('organization_key');
        return UserService::deleteUser($request,$id,'S',$organizationId);
    }

    public function active(Request $request)
    {
        $organizationId = $request->header('organization_key');
        return UserService::activeUsers($request,'S',$organizationId);
    }

    public function addStudentToUser($id,Request $request)
    {
        $organizationId = $request->header('organization_key');
        return StudentService::addStudentContinue($request,$id,$organizationId);
    }

    public function deleteStudent($id,Request $request)
    {
        $organizationId = $request->header('organization_key');
        $studentId = $request->input('student_id');
        return StudentService::deleteStudent($id,$studentId,$request,$organizationId);
    }
}
