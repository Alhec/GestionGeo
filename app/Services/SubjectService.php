<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 24/08/19
 * Time: 03:04 PM
 */

namespace App\Services;

use Illuminate\Http\Request;
use App\Subject;
use App\SchoolProgram;
use App\SchoolProgramSubject;

class SubjectService
{

    const taskError = 'No se puede proceder con la tarea';
    const taskPartialError = 'No se pudo proceder con la tarea en su totalidad';
    const emptySubject = 'No existen materias';
    const notFoundSubject = 'Materia no encontrada';
    const busySubjectCode = 'Codigo de materia en uso';
    const invalidProgram = 'Programas invalidos';
    const ok ='OK';

    public static function getSubjects(Request $request,$organizationId)
    {
        $subjects = Subject::getSubjects($organizationId);
        if (is_numeric($subjects)&&$subjects == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($subjects)>0){
            return $subjects;
        }
        return response()->json(['message'=>self::emptySubject],206);
    }

    public static function getSubjectsById(Request $request,$id,$organizationId)
    {
        $subject = Subject::getSubjectById($id,$organizationId);
        if (is_numeric($subject)&&$subject == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($subject)>0){
           return $subject[0];
        }
        return response()->json(['message'=>self::notFoundSubject],206);
    }

    public static function validate(Request $request)
    {
         $request->validate([
             'subject_code'=>'required|max:10',
             'subject_name'=>'required|max:50',
             'uc'=>'required|numeric',
             'theoretical_hours'=>'required|numeric',
             'practical_hours'=>'required|numeric',
             'laboratory_hours'=>'required|numeric',
             "is_final_subject?"=>'boolean',
             'school_programs.*.id'=>'required|numeric',
             'school_programs.*.type'=>'required|max:2|ends_with:EL,OP,OB',
        ]);
    }

    public static function validateSchoolProgram($schoolPrograms,$organizationId){
        foreach ($schoolPrograms as $schoolProgram){
            if (!SchoolProgram::existSchoolProgramById($schoolProgram['id'],$organizationId)){
                return false;
            }
        }
        return true;
    }

    public static function addSchoolProgramInSubject($schoolPrograms, $subjectId)
    {
        foreach ($schoolPrograms as $schoolProgram){
            $result = SchoolProgramSubject::addSchoolProgramSubject([
                'school_program_id'=>$schoolProgram['id'],
                'subject_id'=>$subjectId,
                'type'=>$schoolProgram['type'],
            ]);
            if (is_numeric($result) && $result == 0){
                return 0;
            }
        }
    }

    public static function addSubject(Request $request,$organizationId)
    {
        self::validate($request);
        $result =Subject::existSubjectByCode($request['subject_code'],$organizationId);
        if (is_numeric($result)&& $result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!$result){
            if (self::validateSchoolProgram($request['school_programs'],$organizationId)){
                $id = Subject::addSubject($request);
                if ($id == 0){
                    return response()->json(['message'=>self::taskError],206);
                }
                $result= self::addSchoolProgramInSubject($request['school_programs'],$id);
                if (is_numeric($result)&& $result == 0){
                    return response()->json(['message'=>self::taskPartialError],206);
                }
                return self::getSubjectsById($request,$id,$organizationId);
            }
            return response()->json(['message'=>self::invalidProgram],206);
        }
        return response()->json(['message'=>self::busySubjectCode],206);
    }

    public static function deleteSubject(Request $request,$id,$organizationId)
    {
        $result = Subject::existSubjectById($id,$organizationId);
        if (is_numeric($result)&& $result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($result){
            $result = Subject::deleteSubject($id);
            if (is_numeric($result)&& $result == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            return response()->json(['message'=>self::ok]);
        }
        return response()->json(['message'=>self::notFoundSubject],206);
    }

    public static function updateSchoolProgramsInSubject($schoolPrograms, $subject_id)
    {
        $result =$schoolProgramsInBd = SchoolProgramSubject::getSchoolProgramSubjectBySubjectId($subject_id);
        if (is_numeric($result)&& $result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        $schoolProgramsUpdated = [];
        foreach ($schoolPrograms as $schoolProgram){
            $existSchoolProgram = false;
            foreach ($schoolProgramsInBd as $schoolProgramInBd){
                if ($schoolProgramInBd['school_program_id']==$schoolProgram['id']){
                    $schoolProgram['subject_id']=$subject_id;
                    $result = SchoolProgramSubject::updateSchoolProgramSubject($schoolProgramInBd['id'],$schoolProgram);
                    if (is_numeric($result)&& $result == 0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                    $schoolProgramsUpdated[]=$schoolProgramInBd['id'];
                    $existSchoolProgram = true;
                    break;
                }
            }
            if ($existSchoolProgram == false) {
                $result =SchoolProgramSubject::addSchoolProgramSubject([
                    'school_program_id'=>$schoolProgram['id'],
                    'subject_id'=>$subject_id,
                    'type'=>$schoolProgram['type'],
                ]);
                if ($result == 0){
                    return response()->json(['message'=>self::taskError],206);
                }
                $postgraduatesUpdated[]=$result;
            }
        }
        foreach ($schoolProgramsInBd as $schoolProgramId){
            if (!in_array($schoolProgramId['id'],$schoolProgramsUpdated)){
               $result = SchoolProgramSubject::deleteSchoolProgramSubject($schoolProgramId['id']);
                if (is_numeric($result)&& $result == 0){
                    return response()->json(['message'=>self::taskError],206);
                }
            }
        }
    }

    public static function updateSubject(Request $request,String $id,$organizationId)
    {
        self::validate($request);
        $result = Subject::existSubjectById($id,$organizationId);
        if (is_numeric($result)&& $result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if($result){
            if (self::validateSchoolProgram($request['school_programs'],$organizationId)){
                $subjectCode = Subject::getSubjectByCode($request['subject_code'],$organizationId);
                if (is_numeric($subjectCode)&& $subjectCode == 0){
                    return response()->json(['message'=>self::taskError],206);
                }
                if (count($subjectCode)>0) {
                    if ($subjectCode[0]['id'] != $id) {
                        return response()->json(['message' => self::busySubjectCode], 206);
                    }
                }
                $result = Subject::updateSubject($id,$request);
                if (is_numeric($result)&& $result == 0){
                    return response()->json(['message'=>self::taskError],206);
                }
                $result = self::updateSchoolProgramsInSubject($request['school_programs'],$id);
                if (is_numeric($result)&& $result == 0){
                    return response()->json(['message'=>self::taskPartialError],206);
                }
                return self::getSubjectsById($request,$id,$organizationId);
            }
            return response()->json(['message'=> self::invalidProgram],206);
        }
        return response()->json(['message'=> self::notFoundSubject],206);
    }
}
