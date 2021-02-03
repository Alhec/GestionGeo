<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SchoolProgramService;

class SchoolProgramController extends Controller
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
        return  $perPage ? SchoolProgramService::getSchoolProgram($organizationId,$perPage) :
            SchoolProgramService::getSchoolProgram($organizationId);
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
        return SchoolProgramService::addSchoolProgram($request,$organizationId);
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
        return SchoolProgramService::getSchoolProgramById($id,$organizationId);
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
        return SchoolProgramService::updateSchoolProgram($request,$id,$organizationId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return SchoolProgramService::deleteSchoolProgram($id,$organizationId);
    }
}
