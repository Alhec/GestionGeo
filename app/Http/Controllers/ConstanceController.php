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
        $organizationId = $request->header('organization_key');
        return ConstanceService::constanceOfStudy($request,$studentId,$organizationId);
    }

    public function academicLoad(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('organization_key');
        return ConstanceService::academicLoad($request,$studentId,$organizationId);
    }

    public function inscriptionConstance(Request $request)
    {
        $studentId = $request->input('student_id');
        $inscriptionId = $request->input('inscription_id');
        $organizationId = $request->header('organization_key');
        return ConstanceService::inscriptionConstance($request,$studentId,$inscriptionId,$organizationId);
    }

    public function studentHistorical(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('organization_key');
        return ConstanceService::studentHistorical($request,$studentId,$organizationId);
    }

    public function teacherHistorical(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        $organizationId = $request->header('organization_key');
        return ConstanceService::teacherHistorical($request,$teacherId,$organizationId);
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
