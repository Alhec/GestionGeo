<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SchoolPeriodService;

class SchoolPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $perPage = $request->input('per_page');
        return $perPage ? SchoolPeriodService::getSchoolPeriods($organizationId,$perPage) :
            SchoolPeriodService::getSchoolPeriods($organizationId);

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
        return SchoolPeriodService::addSchoolPeriod($request,$organizationId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return SchoolPeriodService::getSchoolPeriodById($id,$organizationId);
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
        $organizationId = $request->header('Organization-Key');
        return SchoolPeriodService::updateSchoolPeriod($request,$id,$organizationId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $organizationId = $request->header('Organization-Key');
        return SchoolPeriodService::deleteSchoolPeriod($id,$organizationId);
    }

    public function current(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return SchoolPeriodService::getCurrentSchoolPeriod($organizationId);
    }

    public function subjectTaughtSchoolPeriod(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $teacherId=$request->input('teacher_id');
        return SchoolPeriodService::getSubjectsTaughtSchoolPeriod($teacherId,$organizationId);
    }
}
