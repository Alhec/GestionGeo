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
        $data = $request->input('data');
        return ConstanceService::constanceOfStudy($studentId,$organizationId,$data);
    }

    public function inscriptionConstance(Request $request)
    {
        $studentId = $request->input('student_id');
        $inscriptionId = $request->input('inscription_id');
        $organizationId = $request->header('Organization-Key');
        $data = $request->input('data');
        return ConstanceService::inscriptionConstance($studentId,$inscriptionId,$organizationId,$data);
    }

    public function constanceOfWorkTeacher(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        $organizationId = $request->header('Organization-Key');
        $data = $request->input('data');
        return ConstanceService::constanceOfWorkTeacher($teacherId,$organizationId,$data);
    }

    public function constanceOfWorkAdministrator(Request $request)
    {
        $administratorId = $request->input('administrator_id');
        $organizationId = $request->header('Organization-Key');
        $data = $request->input('data');
        return ConstanceService::constanceOfWorkAdministrator($administratorId,$organizationId,$data);
    }

    public function academicLoad(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('Organization-Key');
        $data = $request->input('data');
        return ConstanceService::academicLoad($studentId,$organizationId,$data);
    }

    public function studentHistorical(Request $request)
    {
        $studentId = $request->input('student_id');
        $organizationId = $request->header('Organization-Key');
        $data = $request->input('data');
        return ConstanceService::studentHistorical($studentId,$organizationId,$data);
    }

}
