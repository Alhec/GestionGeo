<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 26/08/19
 * Time: 09:39 AM
 */

namespace App\Services;

use App\Organization;
use App\User;
use Illuminate\Http\Request;
use App\SchoolPeriod;
use App\Subject;
use App\Teacher;
use App\SchoolPeriodSubjectTeacher;
use App\Schedule;

class SchoolPeriodService
{
    public static function getSchoolPeriods(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $schoolPeriods = SchoolPeriod::getSchoolPeriods($organizationId);
        if (count($schoolPeriods)>0){
            return $schoolPeriods;
        }
        return response()->json(['message'=>'No existen periodos escolares'],206);
    }

    public static function getSchoolPeriodById(Request $request,$id)
    {
        $organizationId = $request->header('organization_key');
        $schoolPeriod = SchoolPeriod::getSchoolPeriodById($id,$organizationId);
        if (count($schoolPeriod)>0){
            return $schoolPeriod[0];
        }
        return response()->json(['message'=>'Periodo escolar no encontrado'],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'cod_school_period'=>'required|max:10',
            'start_date'=>'required|size:10',
            'end_date'=>'required|size:10',
            'withdrawal_deadline'=>'size:10',
            'subjects.*.teacher_id'=>'required|numeric',
            'subjects.*.subject_id'=>'required|numeric',
            'subjects.*.limit'=>'required|numeric',
            'subjects.*.duty'=>'required|numeric',
            'subjects.*.schedules.*.day'=>'required|max:10',
            'subjects.*.schedules.*.classroom'=>'required|max:20',
            'subjects.*.schedules.*.start_hour'=>'required|size:8',
            'subjects.*.schedules.*.end_hour'=>'required|size:8',
        ]);
    }

    public static function validateSubjects($subjects,$organizationId)
    {
        foreach ($subjects as $subject){

            if (!Subject::existSubjectById($subject['subject_id'],$organizationId)){
                return false;
            }
            if (!Teacher::existTeacherById($subject['teacher_id'])){
                return false;
            }else{
                $teacherInBd = Teacher::getTeacherById($subject['teacher_id']);
                if (!User::existUserById($teacherInBd[0]['user_id'],'T',$organizationId)){
                    return false;
                }
            }
        }
        return true;
    }

    public static function subjectConsistency($subjects)
    {
        $subjectId = [];
        foreach ($subjects as $subject){
            if (in_array($subject['subject_id'],$subjectId)){
                return false;
            }
            $subjectId[]=$subject['subject_id'];
        }
        return true;
    }

    public static function addSchedules($schedules,$schoolPeriodSubjectTeacherId)
    {
        foreach ($schedules as $schedule){
            $schedule['school_period_subject_teacher_id']=$schoolPeriodSubjectTeacherId;
            Schedule::addSchedule($schedule);
        }
    }

    public static function addSubjectInSchoolPeriod($subjects,$schoolPeriodId)
    {
        foreach ($subjects as $subject){
            $subject['enrolled_students']=0;
            $subject['school_period_id']=$schoolPeriodId;
            $schoolPeriodSubjectTeacherId = SchoolPeriodSubjectTeacher::addSchoolPeriodSubjectTeacher($subject);
            if (isset($subject['schedules'])){
                self::addSchedules($subject['schedules'],$schoolPeriodSubjectTeacherId);
            }
        }
    }

    public static function addSchoolPeriod(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if(Organization::existOrganization($organizationId)){
            if (!SchoolPeriod::existSchoolPeriodbyCodSchoolPeriod($request['cod_school_period'],$organizationId)){
                $request['load_notes']=false;
                $request['inscription_visible']=false;
                $request['organization_id']=$organizationId;
                if (isset($request['subjects'])){
                    if (!self::validateSubjects($request['subjects'],$organizationId)){
                        return response()->json(['message'=>'Materia o profesor invalido'],206);
                    }
                    if (!self::subjectConsistency($request['subjects'])){
                        return response()->json(['message'=>'Materias duplicadas'],206);
                    }
                    $schoolPeriodId = SchoolPeriod::addSchoolPeriod($request);
                    self::addSubjectInSchoolPeriod($request['subjects'],$schoolPeriodId);
                }else{
                    $schoolPeriodId = SchoolPeriod::addSchoolPeriod($request);
                }
                return self::getSchoolPeriodById($request,$schoolPeriodId);
            }
            return response()->json(['message'=>'Periodo escolar ya registrado'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);
    }

    public static function deleteSchoolPeriod(Request $request,$id)
    {
        $organizationId = $request->header('organization_key');
        if (SchoolPeriod::existSchoolPeriodById($id,$organizationId)){
            SchoolPeriod::deleteSchoolPeriod($id);
            return response()->json(['message'=>'Ok']);
        }
        return response()->json(['message'=>'Periodo escolar no encontrado'],206);
    }

    public static function validateInUpdate(Request $request)
    {
        $request->validate([
            'inscription_visible'=>'required|boolean',
            'load_notes'=>'required|boolean',
        ]);
    }

    public static function updateSchedules($schedules,$schoolPeriodSubjectTeacherId)
    {
        Schedule::deleteAllSchedule($schoolPeriodSubjectTeacherId);
        self::addSchedules($schedules,$schoolPeriodSubjectTeacherId);
    }

    public static function updateSubjectInSchoolPeriod($subjects,$schoolPeriodId)
    {
        $subjectsInBd = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId);
        $subjectsUpdated = [];
        foreach ($subjects as $subject){
            $existSubject = false;
            foreach ($subjectsInBd as $subjectInBd){
                if ($subjectInBd['teacher_id']==$subject['teacher_id'] AND $subjectInBd['subject_id']==$subject['subject_id']){
                    $subject['school_period_id']=$schoolPeriodId;
                    $subject['enrolled_student']=$subjectInBd['enrolled_student'];
                    SchoolPeriodSubjectTeacher::updateSchoolPeriodSubjectTeacher($subjectInBd['id'],$subject);
                    if (isset($subject['schedules'])){
                        self::updateSchedules($subject['schedules'],$subjectInBd['id']);
                    }
                    $subjectsUpdated[]=$subjectInBd['id'];
                    $existSubject=true;
                    break;
                }
            }
            if ($existSubject == false){
                self::addSubjectInSchoolPeriod([$subject],$schoolPeriodId);
                $subjectsUpdated[]=SchoolPeriodSubjectTeacher::findSchoolPeriodSubjectTeacherId($schoolPeriodId,$subject['subject_id'],$subject['teacher_id'])[0]['id'];
            }
        }
        foreach ($subjectsInBd as $subjectId){
            if (!in_array($subjectId['id'],$subjectsUpdated)){
                SchoolPeriodSubjectTeacher::deleteSchoolPeriodSubjectTeacher($subjectId['id']);
            }
        }
    }

    public static function updateSchoolPeriod(Request $request,$id)
    {
        $organizationId = $request->header('organization_key');
        if(Organization::existOrganization($organizationId)){
            $request['organization_id']=$organizationId;
            if (SchoolPeriod::existSchoolPeriodById($id,$organizationId)){
                self::validate($request);
                self::validateInUpdate($request);
                if (SchoolPeriod::existSchoolPeriodbyCodSchoolPeriod($request['cod_school_period'],$organizationId)){
                    if (SchoolPeriod::getSchoolPeriodByCodSchoolPeriod($request['cod_school_period'],$organizationId)[0]['id']!=$id){
                        return response()->json(['message'=>'Periodo escolar ya registrado'],206);
                    }
                }
                if (isset($request['subjects'])){
                    if (!self::validateSubjects($request['subjects'],$organizationId)){
                        return response()->json(['message'=>'Materia o profesor invalido'],206);
                    }
                    if (!self::subjectConsistency($request['subjects'])){
                        return response()->json(['message'=>'Materias duplicadas'],206);
                    }
                    SchoolPeriod::updateSchoolPeriod($id, $request);
                    self::updateSubjectInSchoolPeriod($request['subjects'],$id);
                }else{
                    SchoolPeriod::updateSchoolPeriod($id, $request);
                    if (SchoolPeriodSubjectTeacher::existSchoolPeriodSubjectTeacherBySchoolPeriodId($id)){
                        SchoolPeriodSubjectTeacher::deleteSchoolPeriodSubjectTeacherBySchoolPeriod($id);
                    }
                }
                return self::getSchoolPeriodById($request,$id);
            }
            return response()->json(['message'=>'Periodo escolar no encontrado'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);
    }

    public static function getCurrentSchoolPeriod(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $currentSchoolPeriod = SchoolPeriod::getCurrentSchoolPeriod($organizationId);
        if (count($currentSchoolPeriod)>0){
            return $currentSchoolPeriod[0];
        }
        return response()->json(['message'=>'No hay periodo escolar en curso'],206);
    }

    public static function getSubjectsTaughtSchoolPeriod($teacherId,Request $request)
    {
        $organizationId = $request->header('organization_key');
        $request['teacher_id']=$teacherId;
        $isValid=TeacherService::validateTeacher($request);
        if ($isValid){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (count($currentSchoolPeriod)>0){
                $subjectsTaught = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherBySchoolPeriodTeacher($teacherId,$currentSchoolPeriod[0]['id']);
                if (count($subjectsTaught)>0){
                    return $subjectsTaught;
                }
                return response()->json(['message'=>'No impartes materias en el periodo escolar actual'],206);
            }
            return response()->json(['message'=>'No hay periodo escolar en curso'],206);
        }
        return $isValid;
    }
}
