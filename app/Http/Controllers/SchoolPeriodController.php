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
        return SchoolPeriodService::getSchoolPeriods($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return SchoolPeriodService::addSchoolPeriod($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        return SchoolPeriodService::getSchoolPeriodById($request,$id);
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
        return SchoolPeriodService::updateSchoolPeriod($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        return SchoolPeriodService::deleteSchoolPeriod($request,$id);
    }

    public function current(Request $request)
    {
        return SchoolPeriodService::getCurrentSchoolPeriod($request);
    }
}
