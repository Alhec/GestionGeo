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
        $organizationId = $request->header('organization_key');
        return InscriptionService::getInscriptions($request,$organizationId);
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
        return InscriptionService::addInscription($request,$organizationId);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        return InscriptionService::getInscriptionById($request,$id,$organizationId);
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
        $organizationId = $request->header('organization_key');
        return InscriptionService::deleteInscription($request, $id,$organizationId);
    }

    public function availableSubjects(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $studentId = $request->input('student_id');
        $schoolPeriodId = $request->input('school_period_id');
        return InscriptionService::getAvailableSubjects($studentId,$schoolPeriodId,$request,$organizationId,false);
    }

    public function inscriptionBySchoolPeriod(Request $request,$schoolPeriodId)
    {
        $organizationId = $request->header('organization_key');
        return InscriptionService::getInscriptionsBySchoolPeriod($request,$schoolPeriodId,$organizationId);
    }

    public function studentAvailableSubjects(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $studentId = $request->input('student_id');
        return InscriptionService::studentAvailableSubjects($studentId,$request,$organizationId);
    }

    public  function addStudentInscription(Request $request)
    {
        $organizationId = $request->header('organization_key');
        return InscriptionService::studentAddInscription($request);
    }

    public function currentEnrolledSubjects(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $studentId = $request->input('student_id');
        return InscriptionService::getCurrentEnrolledSubjects($studentId,$organizationId,$request);
    }

    public function withdrawSubjects(Request $request)
    {
        $organizationId = $request->header('organization_key');
        return InscriptionService::withdrawSubjects($request);
    }

    public static function enrolledStudentsInSchoolPeriod(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $teacherId = $request->input('teacher_id');
        $schoolPeriodSubjectTeacherId = $request->input('school_period_subject_teacher_id');
        return InscriptionService::getEnrolledStudentsInSchoolPeriod($teacherId,$schoolPeriodSubjectTeacherId,$organizationId,$request);
    }

    public static function loadNotes(Request $request){
        $organizationId = $request->header('organization_key');
        return InscriptionService::loadNotes($request,$organizationId);
    }

}
