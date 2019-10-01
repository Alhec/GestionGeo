<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InscriptionService;

class InscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return InscriptionService::getInscriptions($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return InscriptionService::addInscription($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        return InscriptionService::getInscriptionById($request,$id);
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
        return InscriptionService::updateInscription($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        return InscriptionService::deleteInscription($request, $id);
    }

    public function availableSubjects(Request $request)
    {
        $studentId = $request->input('student_id');
        $schoolPeriodId = $request->input('school_period_id');
        return InscriptionService::getAvailableSubjects($studentId,$schoolPeriodId,$request,false);
    }

    public function inscriptionBySchoolPeriod(Request $request,$schoolPeriodId)
    {
        return InscriptionService::getInscriptionsBySchoolPeriod($request,$schoolPeriodId);
    }

    public function studentAvailableSubjects(Request $request)
    {
        $studentId = $request->input('student_id');
        return InscriptionService::studentAvailableSubjects($studentId,$request);
    }

    public  function addStudentInscription(Request $request)
    {
        return InscriptionService::studentAddInscription($request);
    }

    public function currentEnrolledSubjects(Request $request)
    {
        $studentId = $request->input('student_id');
        return InscriptionService::getCurrentEnrolledSubjects($studentId,$request);
    }

    public function withdrawSubjects(Request $request)
    {
        return InscriptionService::withdrawSubjects($request);
    }
}
