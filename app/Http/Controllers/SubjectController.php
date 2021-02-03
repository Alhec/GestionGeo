<?php

namespace App\Http\Controllers;

use App\Services\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $perPage = $request->input('per_page');
        return $perPage ? SubjectService::getSubjects($organizationId,$perPage) :
            SubjectService::getSubjects($organizationId);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return SubjectService::addSubject($request,$organizationId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return SubjectService::getSubjectById($id,$organizationId);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return SubjectService::updateSubject($request,$id,$organizationId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
       $organizationId = $request->header('Organization-Key');
       return SubjectService::deleteSubject($id,$organizationId);
    }

    public function getBySchoolProgram($id,Request $request){
        $organizationId = $request->header('Organization-Key');
        return SubjectService::getSubjectsBySchoolProgramId($id,$organizationId);
    }

    public function getSubjectsWithoutFinalWorks(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return SubjectService::getSubjectsWithoutFinalWorks($organizationId);
    }
}
