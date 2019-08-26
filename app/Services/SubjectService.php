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

    /*Elimina el campo organization del cuerpo del response y retorna los postgrados que esten en la organizacion*/
    public static function clearPostgraduate($postgraduates,$organization_id)
    {
        $postgraduatesReturn=[];
        foreach ($postgraduates as $postgraduate){
            if ($postgraduate['organization']['id']==$organization_id){
                unset($postgraduate['organization']);
                $postgraduatesReturn[]=$postgraduate;
            }
        }
        return $postgraduatesReturn;
    }

    public static function getSubjects(Request $request)
    {
        $organization_id = $request->header('organization_key');
        $subjects = Subject::getSubjects();
        $subjectsReturn = [];
        foreach ($subjects as $subject){
            $postgraduates=$subject['postgraduates'];
            unset($subject['postgraduates']);
            $postgraduatesReturn = self::clearPostgraduate($postgraduates,$organization_id);
            if (count($postgraduatesReturn)>0){
                $subject['postgraduates']=$postgraduatesReturn;
                $subjectsReturn[]=$subject;
            }
        }
        if (count($subjectsReturn)>0){
            return $subjectsReturn;
        }else{
            return response()->json(['message'=>'No existen materias'],206);
        }
    }

    public static function getSubjectsById(Request $request, String $id)
    {
        $organization_id = $request->header('organization_key');
        $subject = Subject::getSubjectById($id);
        if ($subject != null){
            $postgraduates=$subject['postgraduates'];
            unset($subject['postgraduates']);
            $postgraduatesReturn = self::clearPostgraduate($postgraduates,$organization_id);
            if (count($postgraduatesReturn)>0){
                $subject['postgraduates']=$postgraduatesReturn;
                return $subject;
            }
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
        ]);
    }

    /*Validar si los postgrados asignados existen en base de datos*/
    public static function validatePostgraduates($postgraduates,$organization_id){

        foreach ($postgraduates as $postgraduate){
            if (!Postgraduate::existPostgraduateById($postgraduate['id'],$organization_id)){
                return false;
            }
        }
        return true;
    }

    public static function addPostgraduatesInSubject($postgraduates, $subject_id)
    {
        foreach ($postgraduates as $postgraduate){
            PostgraduateSubject::addPostgraduateSubject([
                'postgraduate_id'=>$postgraduate['id'],
                'subject_id'=>$subject_id,
                'type'=>$postgraduate['type'],
            ]);
        }
    }

    public static function addSubject(Request $request)
    {
        self::validate($request);
        $organization_id = $request->header('organization_key');
        if (!Subject::existSubject($request['subject_code'])){//Se valida que el postgrado no exista
            if (self::validatePostgraduates($request['postgraduates'],$organization_id)){
                Subject::addSubject($request);
                $subject = Subject::findSubject($request['subject_code']);
                self::addPostgraduatesInSubject($request['postgraduates'],$subject['id']);
                return self::getSubjectsById($request,$subject['id']);
            }
            return response()->json(['message'=>'Postgrados invalidos'],206);
        }
        return response()->json(['message'=>'Materia existente'],206);
    }

    public static function deleteSubject(String $id)
    {
        if (Subject::existSubjectById($id)!=null){
            Subject::deleteSubject($id);
            return response()->json(['message'=>'OK']);
        }else{
            return response()->json(['message'=>'Materia no encontrada'],206);
        }

    }

    public static function updatePostgraduatesInSubject($postgraduates,$subject_id)
    {
        $postgraduatesInBd = PostgraduateSubject::getPostgraduateSubject($subject_id);
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
                $postgraduatesUpdated[]=PostgraduateSubject::findPostgraduateSubject($postgraduate['id'],$subject_id)['id']; //obtengo el id del campo que acabo de insertar
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
        $organization_id = $request->header('organization_key');
        $subject = Subject::existSubjectById($id);
        if($subject!=null){
            if (self::validatePostgraduates($request['postgraduates'],$organization_id)){
                $subjectCode = Subject::findSubject($request['subject_code']);
                if ($subjectCode!=null){
                    if ($subjectCode['id']==$id){
                        Subject::updateSubject($id,$request);
                        self::updatePostgraduatesInSubject($request['postgraduates'],$id);
                    }else{
                        return response()->json(['message'=>'Codigo de materia en uso'],206);
                    }
                }else{
                    Subject::updateSubject($id,$request);
                    self::updatePostgraduatesInSubject($request['postgraduates'],$id);
                }
                return self::getSubjectsById($request,$id);
            }
            return response()->json(['message'=>'Postgrados invalidos'],206);
        }
        return response()->json(['message'=>'Materia no encontrada'],206);
    }
}