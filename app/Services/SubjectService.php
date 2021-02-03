<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 24/08/19
 * Time: 03:04 PM
 */

namespace App\Services;

use App\Log;
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
    const invalidSubjectGroup = 'Las materias que se quieren asociar no se encuentran en el programa escolar';
    const ok ='OK';

    const logCreateSubject = 'Creo la materia ';
    const logUpdateSubject = 'Actualizo la materia ';
    const whitId = ' con id ';
    const logDeleteSubject = 'Elimino la materia ';


    public static function getSubjects($organizationId,$perPage=0)
    {
        $perPage == 0 ? $subjects = Subject::getSubjects($organizationId) :
            $subjects = Subject::getSubjects($organizationId,$perPage);
        if (is_numeric($subjects)&&$subjects == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($perPage == 0){
            if (count($subjects)>0){
                return $subjects;
            }
            return response()->json(['message'=>self::emptySubject],206);
        }else{
            return $subjects;
        }

    }

    public static function getSubjectById($id, $organizationId)
    {
        $subject = Subject::getSubjectById($id,$organizationId);
        if (is_numeric($subject)&&$subject == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($subject)>0){
            for ($i=0;$i<count($subject[0]['schoolPrograms']);$i++){
                $subjectGroup = SchoolProgramSubject::getSubjectGroup($subject[0]['schoolPrograms'][$i]['schoolProgramSubject']['subject_group']);
                $subjects = [];
                foreach ($subjectGroup as $subjectId){
                    if ($subjectId['subject_id']!=$subject[0]['id']){
                        $subjectBD = Subject::getSimpleSubjectById($subjectId['subject_id'],$organizationId);
                        if (is_numeric($subjectBD)&&$subjectBD===0){
                            return response()->json(['message'=>self::taskError],206);
                        }
                        if (count($subjectBD)>0){
                            $subjects[] = $subjectBD[0];
                        }
                    }
                }
                if (count($subjects)>0){
                    $subject[0]['schoolPrograms'][$i]['schoolProgramSubject']['group']=$subjects;
                }
            }
            return $subject[0];
        }
        return response()->json(['message'=>self::notFoundSubject],206);
    }

    public static function validate(Request $request)
    {
         $request->validate([
             'code'=>'required|max:10',
             'name'=>'required|max:50',
             'uc'=>'required|numeric',
             'is_final_subject'=>'boolean',
             'is_project_subject'=>'boolean',
             'theoretical_hours'=>'required|numeric',
             'practical_hours'=>'required|numeric',
             'laboratory_hours'=>'required|numeric',
             'school_programs.*.school_program_id'=>'required|numeric',
             'school_programs.*.type'=>'required|max:2|ends_with:EL,OP,OB',
             'school_programs.*.with_subjects.*.subject_id'=>'numeric',
        ]);
    }

    public static function validateSchoolProgram($schoolPrograms,$organizationId){
        $schoolProgramsId = [];
        $schoolProgramsInBd= SchoolProgram::getSchoolProgram($organizationId);
        if (is_numeric($schoolProgramsInBd) && $schoolProgramsInBd === 0){
            return 0;
        }
        if (count($schoolProgramsInBd)<1){
            return false;
        }
        foreach ($schoolProgramsInBd as $schoolProgramInBd){
            $schoolProgramsId[]=$schoolProgramInBd['id'];
        }
        foreach ($schoolPrograms as $schoolProgram){
            if (!in_array($schoolProgram['school_program_id'],$schoolProgramsId)){
                return false;
            }
        }
        return true;
    }

    public static function updateSubjectToFinalOrProject($subjectId,$organizationId,$subjectAssociatedId)
    {
        $subject = Subject::getSimpleSubjectById($subjectId,$organizationId);
        if (is_numeric($subject)&&$subject===0){
            return 0;
        }
        $subjectAssociated = Subject::getSimpleSubjectById($subjectAssociatedId,$organizationId);
        if (is_numeric($subjectAssociated)&&$subjectAssociated===0){
            return 0;
        }
        if ($subject[0]['is_final_subject'] || $subject[0]['is_project_subject']){
            if ($subject[0]['is_final_subject']){
                $subjectAssociated[0]['is_final_subject']=$subject[0]['is_final_subject'];
            }else if ($subject[0]['is_project_subject']){
                $subjectAssociated[0]['is_project_subject']=$subject[0]['is_project_subject'];
            }
            $result=Subject::updateSubjectLikeArray($subjectAssociatedId,$subjectAssociated[0]->toArray());
            if (is_numeric($result) && $result === 0){
                return 0;
            }
        }else if ($subjectAssociated[0]['is_final_subject'] || $subjectAssociated[0]['is_project_subject']){

            if ($subjectAssociated[0]['is_final_subject']){
                $subject[0]['is_final_subject']=$subjectAssociated[0]['is_final_subject'];
            }else if($subjectAssociated[0]['is_project_subject']){
                $subject[0]['is_project_subject']=$subjectAssociated[0]['is_project_subject'];
            }
            $result=Subject::updateSubjectLikeArray($subjectId,$subject[0]->toArray());
            if (is_numeric($result) && $result === 0){
                return 0;
            }
        }
    }

    public static function addSchoolProgramInSubject($schoolPrograms, $subjectId,$organizationId)
    {
        foreach ($schoolPrograms as $schoolProgram){
            $schoolProgramSubjectId = SchoolProgramSubject::addSchoolProgramSubject([
                'school_program_id'=>$schoolProgram['school_program_id'],
                'subject_id'=>$subjectId,
                'type'=>$schoolProgram['type']
            ]);
            if (is_numeric($schoolProgramSubjectId )&&$schoolProgramSubjectId ==0){
                return 0;
            }else{
                $result = self::updateSchoolProgramSubject($schoolProgramSubjectId,$schoolProgram['school_program_id'],
                    $subjectId,$schoolProgram['type'],$schoolProgramSubjectId);
                if (is_numeric($result) && $result == 0){
                    return 0;
                }
            }
            if (isset($schoolProgram['with_subjects'])){
                foreach($schoolProgram['with_subjects'] as $withSubjectId){
                    $subject = SchoolProgramSubject::getSchoolProgramSubjectBySubjectAndSchoolProgram(
                        $withSubjectId['subject_id'],$schoolProgram['school_program_id']);
                    if (is_numeric($subject) && $subject ===0){
                        return 0;
                    }
                    if (count($subject)>0){
                        $subject = $subject[0];
                        $result=self::updateSchoolProgramSubject($subject['id'],$subject['school_program_id'],
                            $subject['subject_id'],$subject['type'],$schoolProgramSubjectId);
                        if (is_numeric($result) && $result === 0){
                            return 0;
                        }
                       if ($subject['subject_group']!==$schoolProgramSubjectId){
                            $subjectsInGroup = SchoolProgramSubject::getSubjectGroup($subject['subject_group']);
                            if (is_numeric($subjectsInGroup)&&$subjectsInGroup ===0 ){
                                return 0;
                            }
                            if (count($subjectsInGroup)>0){
                                foreach ($subjectsInGroup as $subjectInGroup){
                                   $result = self::updateSchoolProgramSubject($subjectInGroup['id'],
                                       $subjectInGroup['school_program_id'],$subjectInGroup['subject_id'],
                                       $subjectInGroup['type'],$subjectInGroup['id']);
                                    if (is_numeric($result) && $result == 0){
                                        return 0;
                                    }
                                }
                            }
                        }
                        $result = self::updateSubjectToFinalOrProject($withSubjectId['subject_id'],$organizationId,$subjectId);
                        if (is_numeric($result) && $result === 0){
                            return 0;
                        }
                    }
                }
            }
        }
    }

    public static function updateSchoolProgramSubject($id,$schoolProgramId,$subjectId,$type,$subjectGroup)
    {
        $result=SchoolProgramSubject::updateSchoolProgramSubject($id,
            [
                'school_program_id'=>$schoolProgramId,
                'subject_id'=>$subjectId,
                'type'=>$type,
                'subject_group'=>$subjectGroup
            ]);
        if (is_numeric($result) && $result == 0){
            return 0;
        }
    }

    public static function validateSubjectGroup($schoolPrograms,$organizationId)
    {
        foreach ($schoolPrograms as $schoolProgram){
            $subjectsInBd = Subject::getSubjectsBySchoolProgram($schoolProgram['school_program_id'],$organizationId);
            if (is_numeric($subjectsInBd) && $subjectsInBd==0){
                return 0;
            }
            if (count($subjectsInBd)<1){
                return false;
            }
            if (isset($schoolProgram['with_subjects'])){
                $subjectsId = array_column($schoolProgram['with_subjects'],'id');
                $subjectsIdInBd = array_column($subjectsInBd->toArray(),'id');
                foreach ($subjectsId as $subjectId){
                    if (!in_array($subjectId, $subjectsIdInBd)){
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public static function addSubject(Request $request,$organizationId)
    {
        self::validate($request);
        $result =Subject::existSubjectByCode($request['code'],$organizationId);
        if (is_numeric($result)&& $result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!$result){
            $validateSchoolProgram = self::validateSchoolProgram($request['school_programs'],$organizationId);
            if (is_numeric($validateSchoolProgram) && $validateSchoolProgram === 0){
                return response()->json(['message'=>self::taskError],206);
            }
            if ($validateSchoolProgram){
                $validateSubjectGroup = self::validateSubjectGroup($request['school_programs'],$organizationId);
                if($validateSubjectGroup === 0){
                    return response()->json(['message'=>self::taskError],206);

                }
                if ($validateSubjectGroup){
                    $id = Subject::addSubject($request);
                    if ($id === 0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                    $result= self::addSchoolProgramInSubject($request['school_programs'],$id,$organizationId);
                    if (is_numeric($result)&& $result == 0){
                        return response()->json(['message'=>self::taskPartialError],206);
                    }
                    $log = Log::addLog(auth('api')->user()['id'],self::logCreateSubject.$request['name'].
                        self::whitId.$id);
                    if (is_numeric($log)&&$log==0){
                        return response()->json(['message'=>self::taskPartialError],401);
                    }
                    return self::getSubjectById($id,$organizationId);
                }
                return response()->json(['message'=>self::invalidSubjectGroup],206);
            }
            return response()->json(['message'=>self::invalidProgram],206);
        }
        return response()->json(['message'=>self::busySubjectCode],206);
    }

    public static function deleteSubject($id,$organizationId)
    {
        $subject = Subject::getSubjectById($id,$organizationId);
        if (is_numeric($subject)&& $subject == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($subject)>0){
            $result = Subject::deleteSubject($id);
            if (is_numeric($result)&& $result == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logDeleteSubject.$subject[0]['name'].
                self::whitId.$subject[0]['id']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskPartialError],401);
            }
            return response()->json(['message'=>self::ok]);
        }
        return response()->json(['message'=>self::notFoundSubject],206);
    }

    public static function defineNewSubjectGroup($subject){
        $schoolProgramSubject = SchoolProgramSubject::getSchoolProgramSubjectBySubjectAndSchoolProgram($subject['subject_id'],$subject['school_program_id']);
        if (is_numeric($schoolProgramSubject)&&$schoolProgramSubject===0){
            return 0;
        }
        if (count($schoolProgramSubject)>0){
            $subjectsGroup = SchoolProgramSubject::getSubjectGroup($schoolProgramSubject[0]['subject_group']);
            if (is_numeric($subjectsGroup)&&$subjectsGroup===0){
                return 0;
            }
            if (count($subjectsGroup)>0){
                foreach ($subjectsGroup as $subjectGroup){
                    if ($subjectGroup['subject_id']!=$subject['subject_id']){
                        $result = self::updateSchoolProgramSubject($subjectGroup['id'],$subjectGroup['school_program_id'],
                            $subjectGroup['subject_id'],$subjectGroup['type'],$subjectGroup['id']);
                        if ($result ===0){
                            return 0;
                        }
                    }
                }
            }
        }
    }

    public static function updateSchoolProgramSubjectsInSubject($schoolPrograms, $subjectId,$organizationId)
    {
        $subjectId = intval($subjectId);
        $schoolProgramSubjectsInBd = SchoolProgramSubject::getSchoolProgramSubjectsBySubjectId($subjectId);
        if (is_numeric($schoolProgramSubjectsInBd)&& $schoolProgramSubjectsInBd == 0){
            return 0;
        }
        $schoolProgramSubjectsUpdated = [];
        foreach ($schoolPrograms as $schoolProgram){
            $existSchoolProgram = false;
            foreach ($schoolProgramSubjectsInBd as $schoolProgramSubjectInBd){
                if ($schoolProgramSubjectInBd['school_program_id']==$schoolProgram['school_program_id']){
                    $schoolProgram['subject_id']=$subjectId;
                    if (isset($schoolProgram['with_subjects'])){
                        $schoolProgram['subject_group']=$schoolProgramSubjectInBd['subject_group'];
                        $result = SchoolProgramSubject::updateSchoolProgramSubject($schoolProgramSubjectInBd['id'],
                            $schoolProgram);
                        if (is_numeric($result)&& $result == 0){
                            return 0;
                        }
                        $schoolProgramSubjectsUpdated[] = $schoolProgramSubjectInBd['id'];
                        $subjectGroup = SchoolProgramSubject::getSubjectGroup($schoolProgram['subject_group']);
                        if (is_numeric($subjectGroup)&&$subjectGroup===0){
                            return 0;
                        }
                        if (count($subjectGroup)>0){
                            $subjectGroupSubjectId = array_column($subjectGroup->toArray(),'subject_id');
                            $subjectsJoin = [];
                            foreach ($schoolProgram['with_subjects'] as $subjectIdJoin){
                                if (!in_array($subjectIdJoin['subject_id'],$subjectGroupSubjectId)){
                                    $schoolProgramSubject = SchoolProgramSubject::getSchoolProgramSubjectBySubjectAndSchoolProgram(
                                        $subjectIdJoin['subject_id'],$schoolProgram['school_program_id']);
                                    if (is_numeric($schoolProgramSubject)&&$schoolProgramSubject==0){
                                        return 0;
                                    }
                                    $schoolProgramSubject[0]['subject_group']=$schoolProgram['subject_group'];
                                    $result=SchoolProgramSubject::updateSchoolProgramSubject($schoolProgramSubject[0]['id'],
                                        $schoolProgramSubject[0]->toArray());
                                    if (is_numeric($result)&& $result == 0){
                                        return 0;
                                    }
                                    $subjectsJoin[] = $subjectIdJoin['subject_id'];
                                }else{
                                    $subjectsJoin[] = $subjectIdJoin['subject_id'];
                                }
                                $result = self::updateSubjectToFinalOrProject($subjectIdJoin['subject_id'],$organizationId,$subjectId);
                                if (is_numeric($result)&& $result == 0){
                                    return 0;
                                }
                            }
                            foreach ($subjectGroup as $aSubjectGroup){
                                if (!in_array($aSubjectGroup['subject_id'],$subjectsJoin)){
                                    $schoolProgramSubject = SchoolProgramSubject::getSchoolProgramSubjectBySubjectAndSchoolProgram(
                                        $aSubjectGroup['subject_id'],$schoolProgram['school_program_id']);
                                    if (is_numeric($schoolProgramSubject)&&$schoolProgramSubject==0){
                                        return 0;
                                    }
                                    $schoolProgramSubject[0]['subject_group']=$schoolProgramSubject[0]['id'];
                                    $result=SchoolProgramSubject::updateSchoolProgramSubject($schoolProgramSubject[0]['id'],
                                        $schoolProgramSubject[0]->toArray());
                                    if (is_numeric($result)&& $result == 0){
                                        return 0;
                                    }
                                }
                            }
                        }
                    }else{
                        $result = self::defineNewSubjectGroup($schoolProgram);
                        if (is_numeric($result)&& $result == 0){
                            return 0;
                        }
                        $schoolProgram['subject_group']=$schoolProgramSubjectInBd['id'];
                        $result = SchoolProgramSubject::updateSchoolProgramSubject($schoolProgramSubjectInBd['id'],
                            $schoolProgram);
                        if (is_numeric($result)&& $result == 0){
                            return 0;
                        }
                    }
                    $schoolProgramSubjectsUpdated[]=$schoolProgramSubjectInBd['id'];
                    $existSchoolProgram = true;
                    break;
                }
            }
            if ($existSchoolProgram == false) {
                self::addSchoolProgramInSubject([$schoolProgram],$subjectId,$organizationId);
                $idSchoolProgramSubject = SchoolProgramSubject::getSchoolProgramSubjectBySubjectAndSchoolProgram($subjectId,
                    $schoolProgram['school_program_id']);
                if (is_numeric($idSchoolProgramSubject)&&$idSchoolProgramSubject==0){
                    return 0;
                }
                $postgraduatesUpdated[]=$idSchoolProgramSubject[0]['id'];
            }
        }
        foreach ($schoolProgramSubjectsInBd as $schoolProgramId){
            if (!in_array($schoolProgramId['id'],$schoolProgramSubjectsUpdated)){
               $result = SchoolProgramSubject::deleteSchoolProgramSubject($schoolProgramId['id']);
                if (is_numeric($result)&& $result == 0){
                    return 0;
                }
            }
        }
    }

    public static function updateSubject(Request $request, $id,$organizationId)
    {
        self::validate($request);
        $result = Subject::existSubjectById($id,$organizationId);
        if (is_numeric($result)&& $result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if($result){
            $validateSchoolProgram = self::validateSchoolProgram($request['school_programs'],$organizationId);
            if (is_numeric($validateSchoolProgram) && $validateSchoolProgram === 0){
                return response()->json(['message'=>self::taskError],206);
            }
            if ($validateSchoolProgram){
                $subjectCode = Subject::getSubjectByCode($request['code'],$organizationId);
                if (is_numeric($subjectCode)&& $subjectCode == 0){
                    return response()->json(['message'=>self::taskError],206);
                }
                if (count($subjectCode)>0) {
                    if ($subjectCode[0]['id'] != $id) {
                        return response()->json(['message' => self::busySubjectCode], 206);
                    }
                }
                $validateSubjectGroup = self::validateSubjectGroup($request['school_programs'],$organizationId);
                if($validateSubjectGroup === 0){
                    return response()->json(['message'=>self::taskError],206);
                }
                if ($validateSubjectGroup){
                    $result = Subject::updateSubject($id,$request);
                    if (is_numeric($result)&& $result == 0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                    $result = self::updateSchoolProgramSubjectsInSubject($request['school_programs'],$id,$organizationId);
                    if (is_numeric($result)&& $result == 0){
                        return response()->json(['message'=>self::taskPartialError],206);
                    }
                    $log = Log::addLog(auth('api')->user()['id'],self::logUpdateSubject.$request['name'].
                        self::whitId.$id);
                    if (is_numeric($log)&&$log==0){
                        return response()->json(['message'=>self::taskPartialError],401);
                    }
                    return self::getSubjectById($id,$organizationId);
                }
            }
            return response()->json(['message'=> self::invalidProgram],206);
        }
        return response()->json(['message'=> self::notFoundSubject],206);
    }

    public static function getSubjectsBySchoolProgramId($schoolProgramId, $organizationId)
    {
        $subjects = Subject::getSubjectsBySchoolProgram($schoolProgramId,$organizationId);
        if (is_numeric($subjects)&&$subjects===0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($subjects)>0){
            return $subjects;
        }
        return response()->json(['message'=>self::emptySubject],206);
    }

    public static function getSubjectsWithoutFinalWorks($organizationId)
    {
        $subjects = Subject::getSubjectsWithoutFinalWorks($organizationId);
        if (is_numeric($subjects)&&$subjects == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($subjects)>0){
            return $subjects;
        }
        return response()->json(['message'=>self::emptySubject],206);
    }
}
