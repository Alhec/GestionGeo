<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SchoolPeriodService;

class SchoolPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $organizationId = $request->header('organization_key');
        return SchoolPeriodService::getSchoolPeriods($request,$organizationId);
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
        return SchoolPeriodService::addSchoolPeriod($request,$organizationId);
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
        return SchoolPeriodService::getSchoolPeriodById($request,$id,$organizationId);
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
        return SchoolPeriodService::updateSchoolPeriod($request,$id,$organizationId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        return SchoolPeriodService::deleteSchoolPeriod($request,$id,$organizationId);
    }

    public function current(Request $request)
    {
        $organizationId = $request->header('organization_key');
        return SchoolPeriodService::getCurrentSchoolPeriod($request,$organizationId);
    }

    public function subjectTaughtSchoolPeriod(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $teacherId=$request->input('teacher_id');
        return SchoolPeriodService::getSubjectsTaughtSchoolPeriod($teacherId,$request,$organizationId);
    }
}
