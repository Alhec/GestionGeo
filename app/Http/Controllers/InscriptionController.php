<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InscriptionService;

class InscriptionController extends Controller
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
        return InscriptionService::getInscriptions($organizationId);
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
        $organizationId = $request->header('Organization-Key');
        return InscriptionService::getInscriptionById($id,$organizationId);
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
        return InscriptionService::updateInscription($request,$id,$organizationId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return InscriptionService::deleteInscription($id,$organizationId);
    }

    public function availableSubjects(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $studentId = $request->input('student_id');
        $schoolPeriodId = $request->input('school_period_id');
        return InscriptionService::getAvailableSubjects($studentId,$schoolPeriodId,$organizationId,false);
    }

    public function inscriptionBySchoolPeriod(Request $request,$schoolPeriodId)
    {
        $organizationId = $request->header('Organization-Key');
        return InscriptionService::getInscriptionsBySchoolPeriod($schoolPeriodId,$organizationId);
    }

    public function studentAvailableSubjects(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $studentId = $request->input('student_id');
        return InscriptionService::studentAvailableSubjects($studentId,$organizationId);
    }

    public  function addStudentInscription(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return InscriptionService::studentAddInscription($request,$organizationId);
    }

    public function currentEnrolledSubjects(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $studentId = $request->input('student_id');
        return InscriptionService::getCurrentEnrolledSubjects($studentId,$organizationId);
    }

    public function withdrawSubjects(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        return InscriptionService::withdrawSubjects($request,$organizationId);
    }

    public static function enrolledStudentsInSchoolPeriod(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $teacherId = $request->input('teacher_id');
        $schoolPeriodSubjectTeacherId = $request->input('school_period_subject_teacher_id');
        return InscriptionService::getEnrolledStudentsInSchoolPeriod($teacherId,$schoolPeriodSubjectTeacherId,
            $organizationId);
    }

    public static function loadNotes(Request $request){
        $organizationId = $request->header('Organization-Key');
        return InscriptionService::loadNotes($request,$organizationId);
    }

    public static function deleteFinalWork($id){
        return InscriptionService::deleteFinalWork($id);
    }

}
