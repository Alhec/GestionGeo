<?php

namespace App\Http\Controllers;

use App\Services\ConstanceService;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;

class ConstanceController extends Controller
{
    public function constanceOfStudy(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('Organization-Key');
        return ConstanceService::constanceOfStudy($request,$studentId,$organizationId);
    }

    public function academicLoad(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('Organization-Key');
        return ConstanceService::academicLoad($request,$studentId,$organizationId);
    }

    public function inscriptionConstance(Request $request)
    {
        $studentId = $request->input('student_id');
        $inscriptionId = $request->input('inscription_id');
        $organizationId = $request->header('Organization-Key');
        return ConstanceService::inscriptionConstance($request,$studentId,$inscriptionId,$organizationId);
    }

    public function studentHistorical(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('Organization-Key');
        return ConstanceService::studentHistorical($request,$studentId,$organizationId);
    }

    public function studentHistoricalData(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('Organization-Key');
        return ConstanceService::studentHistoricalData($request,$studentId,$organizationId);
    }

    public function constanceOfWorkTeacher(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        $organizationId = $request->header('Organization-Key');
        return ConstanceService::constanceOfWorkTeacher($request,$teacherId,$organizationId);
    }

    public function constanceOfWorkAdministrator(Request $request)
    {
        $administratorId = $request->input('administrator_id');
        $organizationId = $request->header('Organization-Key');
        return ConstanceService::constanceOfWorkAdministrator($request,$administratorId,$organizationId);
    }
}
