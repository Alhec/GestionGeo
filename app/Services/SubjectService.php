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

    public static function getSubjects(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $subjects = Subject::getSubjects($organizationId);
        if (count($subjects)>0){
            return $subjects;
        }
        return response()->json(['message'=>'No existen materias'],206);
    }

    public static function getSubjectsById(Request $request,$id)
    {
        $organizationId = $request->header('organization_key');
        $subject = Subject::getSubjectById($id,$organizationId);
        if (count($subject)>0){
           return $subject[0];
        }
        return response()->json(['message'=>'Materia no encontrada'],206);
    }

    public static function validate(Request $request)
    {
         $request->validate([
             'subject_code'=>'required|max:10',
             'subject_name'=>'required|max:50',
             'uc'=>'required|numeric',
             'subject_type'=>'max:3|ends_with:REG,AMP,ACT,PER,PDC',
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
            SchoolProgramSubject::addSchoolProgramSubject([
                'school_program_id'=>$schoolProgram['id'],
                'subject_id'=>$subjectId,
                'type'=>$schoolProgram['type'],
            ]);
        }
    }

    public static function addSubject(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (!Subject::existSubjectByCode($request['subject_code'],$organizationId)){
            if (self::validateSchoolProgram($request['school_programs'],$organizationId)){
                $id = Subject::addSubject($request);
                self::addSchoolProgramInSubject($request['school_programs'],$id);
                return self::getSubjectsById($request,$id);
            }
            return response()->json(['message'=>'Postgrados invalidos'],206);
        }
        return response()->json(['message'=>'Codigo de materia en uso'],206);
    }

    /*public static function validateSubjectInOrganization($subjectId,$organizationId)
    {
        $postgraduates = Postgraduate::getPostgraduates($organizationId);
        $postgraduateSubjects = PostgraduateSubject::getPostgraduateSubjectBySubjectId($subjectId);
        foreach ($postgraduates as $postgraduate){
            foreach ($postgraduateSubjects as $postgraduateSubject){
                if ($postgraduateSubject['postgraduate_id']==$postgraduate['id']){
                    return true;
                }
            }
        }
        return false;
    }*/

    public static function deleteSubject(Request $request,$id)
    {
        $organizationId = $request->header('organization_key');
        if (Subject::existSubjectById($id,$organizationId)){
            Subject::deleteSubject($id);
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Materia no encontrada'],206);
    }

    public static function updateSchoolProgramsInSubject($schoolPrograms, $subject_id)
    {
        $schoolProgramsInBd = SchoolProgramSubject::getSchoolProgramSubjectBySubjectId($subject_id);
        $schoolProgramsUpdated = [];
        foreach ($schoolPrograms as $schoolProgram){
            $existSchoolProgram = false;
            foreach ($schoolProgramsInBd as $schoolProgramInBd){
                if ($schoolProgramInBd['school_program_id']==$schoolProgram['id']){
                    $schoolProgram['subject_id']=$subject_id;
                    SchoolProgramSubject::updateSchoolProgramSubject($schoolProgramInBd['id'],$schoolProgram);
                    $schoolProgramsUpdated[]=$schoolProgramInBd['id'];
                    $existSchoolProgram = true;
                    break;
                }
            }
            if ($existSchoolProgram == false){
                $postgraduatesUpdated[]=SchoolProgramSubject::addSchoolProgramSubject([
                    'school_program_id'=>$schoolProgram['id'],
                    'subject_id'=>$subject_id,
                    'type'=>$schoolProgram['type'],
                ]);
            }
        }
        foreach ($schoolProgramsInBd as $schoolProgramId){
            if (!in_array($schoolProgramId['id'],$schoolProgramsUpdated)){
               SchoolProgramSubject::deleteSchoolProgramSubject($schoolProgramId['id']);
            }
        }
    }

    public static function updateSubject(Request $request,String $id)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if(Subject::existSubjectById($id,$organizationId)){
            if (self::validateSchoolProgram($request['school_programs'],$organizationId)){
                $subjectCode = Subject::getSubjectByCode($request['subject_code'],$organizationId);
                if (count($subjectCode)>0) {
                    if ($subjectCode[0]['id'] != $id) {
                        return response()->json(['message' => 'Codigo de materia en uso'], 206);
                    }
                }
                Subject::updateSubject($id,$request);
                self::updateSchoolProgramsInSubject($request['school_programs'],$id);
                return self::getSubjectsById($request,$id);
            }
            return response()->json(['message'=>'Postgrados invalidos'],206);
        }
        return response()->json(['message'=>'Materia no encontrada'],206);
    }
}
