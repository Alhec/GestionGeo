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
use App\Postgraduate;
use App\PostgraduateSubject;

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
             'subject_type'=>'max:3|ends_with:REG,AMP',
             'postgraduates.*.id'=>'numeric',
             'postgraduates.*.type'=>'max:1|ends_with:E,O',
        ]);
    }

    public static function validatePostgraduates($postgraduates,$organizationId){
        foreach ($postgraduates as $postgraduate){
            if (!Postgraduate::existPostgraduateById($postgraduate['id'],$organizationId)){
                return false;
            }
        }
        return true;
    }

    public static function addPostgraduatesInSubject($postgraduates, $subjectId)
    {
        foreach ($postgraduates as $postgraduate){
            PostgraduateSubject::addPostgraduateSubject([
                'postgraduate_id'=>$postgraduate['id'],
                'subject_id'=>$subjectId,
                'type'=>$postgraduate['type'],
            ]);
        }
    }

    public static function addSubject(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (!Subject::existSubjectByCode($request['subject_code'],$organizationId)){
            if (self::validatePostgraduates($request['postgraduates'],$organizationId)){
                Subject::addSubject($request);
                $subject = Subject::getSubjectByParameters($request['subject_code'],$request['subject_name'],$request['uc'])[0];
                self::addPostgraduatesInSubject($request['postgraduates'],$subject['id']);
                return self::getSubjectsById($request,$subject['id']);
            }
            return response()->json(['message'=>'Postgrados invalidos'],206);
        }
        return response()->json(['message'=>'Codigo de materia en uso'],206);
    }

    public static function validateSubjectInOrganization($subjectId,$organizationId)
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
    }

    public static function deleteSubject(Request $request,$id)
    {
        $organizationId = $request->header('organization_key');
        if (Subject::existSubjectById($id,$organizationId)){
            Subject::deleteSubject($id);
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Materia no encontrada'],206);
    }

    public static function updatePostgraduatesInSubject($postgraduates,$subject_id)
    {
        $postgraduatesInBd = PostgraduateSubject::getPostgraduateSubjectBySubjectId($subject_id);
        $postgraduatesUpdated = [];
        foreach ($postgraduates as $postgraduate){
            $existPostgraduate = false;
            foreach ($postgraduatesInBd as $postgraduateInBd){
                if ($postgraduateInBd['postgraduate_id']==$postgraduate['id']){
                    $postgraduate['subject_id']=$subject_id;
                    PostgraduateSubject::updatePostgraduateSubject($postgraduateInBd['id'],$postgraduate);
                    $postgraduatesUpdated[]=$postgraduateInBd['id'];
                    $existPostgraduate = true;
                    break;
                }
            }
            if ($existPostgraduate ==false){
                PostgraduateSubject::addPostgraduateSubject([
                    'postgraduate_id'=>$postgraduate['id'],
                    'subject_id'=>$subject_id,
                    'type'=>$postgraduate['type'],
                ]);
                $postgraduatesUpdated[]=PostgraduateSubject::getPostgraduateSubject($postgraduate['id'],$subject_id)[0]['id'];
            }
        }
        foreach ($postgraduatesInBd as $postgraduateId){
            if (!in_array($postgraduateId['id'],$postgraduatesUpdated)){
               PostgraduateSubject::deletePostgraduateSubject($postgraduateId['id']);
            }
        }
    }

    public static function updateSubject(Request $request,String $id)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if(Subject::existSubjectById($id,$organizationId)){
            if (self::validatePostgraduates($request['postgraduates'],$organizationId)){
                $subjectCode = Subject::getSubjectByCode($request['subject_code'],$organizationId);
                if (count($subjectCode)>0) {
                    if ($subjectCode[0]['id'] != $id) {
                        return response()->json(['message' => 'Codigo de materia en uso'], 206);
                    }
                }
                Subject::updateSubject($id,$request);
                self::updatePostgraduatesInSubject($request['postgraduates'],$id);
                return self::getSubjectsById($request,$id);
            }
            return response()->json(['message'=>'Postgrados invalidos'],206);
        }
        return response()->json(['message'=>'Materia no encontrada'],206);
    }
}