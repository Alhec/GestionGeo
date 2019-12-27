<?php

namespace App\Http\Controllers;

use App\Services\ConstanceService;
use Illuminate\Http\Request;

class ConstanceController extends Controller
{
    public function constanceOfStudy(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('organization_key');
        return ConstanceService::constanceOfStudy($request,$studentId,$organizationId);
    }

    public function academicLoad(Request $request)
    {
        $studentId = $request->input('student_id');
        return ConstanceService::academicLoad($request,$studentId);
    }

    public function studentHistorical(Request $request)
    {
        $studentId = $request->input('student_id');
        return ConstanceService::studentHistorical($request,$studentId);
    }

    public function studentHistoricalAllData(Request $request)
    {
        $studentId = $request->input('student_id');
        return ConstanceService::studentHistoricalAllData($request,$studentId);
    }

    public function inscriptionConstance(Request $request)
    {
        $studentId = $request->input('student_id');
        $inscriptionId = $request->input('inscription_id');
        return ConstanceService::inscriptionConstance($request,$studentId,$inscriptionId);
    }

    public function teacherHistorical(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        return ConstanceService::teacherHistorical($request,$teacherId);
    }

    public function constanceOfWorkTeacher(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        return ConstanceService::constanceOfWorkTeacher($request,$teacherId);
    }

    public function constanceOfWorkAdministrator(Request $request)
    {
        $administratorId = $request->input('administrator_id');
        return ConstanceService::constanceOfWorkAdministrator($request,$administratorId);
    }
}
