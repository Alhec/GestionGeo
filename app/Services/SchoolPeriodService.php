<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 26/08/19
 * Time: 09:39 AM
 */

namespace App\Services;

use Illuminate\Http\Request;
use App\SchoolPeriod;
use App\Postgraduate;
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
            'start_date'=>'required',
            'end_date'=>'required',
            'subjects.*.teacher_id'=>'required|numeric',
            'subjects.*.subject_id'=>'required|numeric',
            'subjects.*.limit'=>'required|numeric',
            'subjects.*.duty'=>'required|numeric',
        ]);
    }

    public static function validateSubjects($subjects)
    {
        foreach ($subjects as $subject){
            if ((Subject::existSubjectById($subject['subject_id'])==null) OR (Teacher::existTeacherById($subject['subject_id'])==null)){
                return false;
            }
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
            SchoolPeriodSubjectTeacher::addSchoolPeriodSubjectTeacher($subject);
            $schoolPeriodSubjectTeacherId= SchoolPeriodSubjectTeacher::findSchoolPeriodSubjectTeacher($schoolPeriodId,$subject['subject_id'],$subject['teacher_id'])[0]['id'];
            if (isset($subject['schedules'])){
                self::addSchedules($subject['schedules'],$schoolPeriodSubjectTeacherId);
            }
        }
    }

    public static function addSchoolPeriod(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if(Postgraduate::existOrganization($organizationId)){
            if (!SchoolPeriod::existSchoolPeriod($request['cod_school_period'],$organizationId)){
                $request['load_notes']=false;
                $request['inscription_visible']=false;
                $request['organization_id']=$organizationId;
                if (isset($request['subjects'])){
                    if (!self::validateSubjects($request['subjects'])){
                        return response()->json(['message'=>'Materia o profesor invalido'],206);
                    }else{
                        SchoolPeriod::addSchoolPeriod($request);
                        $schoolPeriodId = SchoolPeriod::findSchoolPeriodId($request['cod_school_period'],$organizationId)['id'];
                        self::addSubjectInSchoolPeriod($request['subjects'],$schoolPeriodId);
                    }
                }else{
                    SchoolPeriod::addSchoolPeriod($request);
                    $schoolPeriodId = SchoolPeriod::findSchoolPeriodId($request['cod_school_period'],$organizationId)['id'];
                }
                return self::getSchoolPeriodById($request,$schoolPeriodId);
            }
            return response()->json(['message'=>'Periodo escolar ya registrado'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);







        $schoolPeriod = SchoolPeriod::where('cod_school_period',$request['cod_school_period'])->get();
        if (count($schoolPeriod)>0){
            return response()->json(['message'=>'Ya existe periodo escolar'],206);
        }else{//el postgrado puede ser ingresado si sus relaciones estan correctas
            if (isset($request['subject'])){
                $subjects = $request['subject'];
                $teachersInBd=Teacher::all('id');
                $subjectsInBd=Subject::all('id');
                $validRelation = true;
                foreach ($subjects as $subject){//recorrer las relaciones para validar que la materia y el profesor asignado sean correctos
                    $foundTeacher = false;
                    foreach ($teachersInBd as $teacherInBd){//validar profesor
                        if (($subject['teacher_id']!=$teacherInBd['id'])AND $foundTeacher ==false){
                            $validRelation = false;
                        }else{
                            $foundTeacher=true;
                        }
                    }
                    if ($foundTeacher == true){//si se consigue el profesor entonces se validan las materias
                        $foundSubject = false;
                        foreach ($subjectsInBd as $subjectInBd){// recorrer para validar si lasmaterias estan en bd
                            if (($subject['subject_id']!=$subjectInBd['id'])AND $foundSubject ==false){
                                $validRelation = false;
                            }else{
                                $foundSubject=true;
                                $validRelation = true;
                            }
                        }
                    }
                }
                if ($validRelation == true){// si la relacion de asociacion es correct se procede  insertar en bd
                    SchoolPeriod::create($request->all());
                    $schoolPeriod = SchoolPeriod::where('cod_school_period',$request['cod_school_period'])->get('id')[0];
                    foreach ($subjects as $subject){ // Puede haber mas de una materia con profesor a ser agregada
                        SchoolPeriodSubjectTeacher::create([
                            'teacher_id'=>$subject['teacher_id'],
                            'subject_id'=>$subject['subject_id'],
                            'school_period_id'=>$schoolPeriod['id'],
                            'inscription_visible'=>$subject['inscription_visible'],
                            'limit'=>$subject['limit'],
                            'enrolled_students'=>$subject['enrolled_students'],
                            'load_notes'=>$subject['load_notes'],
                            'duty'=>$subject['duty'],
                        ]);
                        if (isset($subject['schedule'])){//valido si asignaron horarios a las materias
                            $schedules = $subject['schedule'];
                            $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::where('teacher_id',$subject['teacher_id'])
                                ->where('subject_id',$subject['subject_id'])->where('school_period_id',$schoolPeriod['id'])->get('id')[0];
                            foreach ($schedules as $schedule){
                                Schedule::Create([
                                    'school_period_subject_teacher_id'=>$schoolPeriodSubjectTeacher['id'],
                                    'day'=>$schedule['day'],
                                    'classroom'=>$schedule['classroom'],
                                    'start_hour'=>$schedule['start_hour'],
                                    'end_hour'=>$schedule['end_hour'],
                                ]);
                            }
                        }
                    }
                    $schoolPeriod = SchoolPeriod::where('cod_school_period',$request['cod_school_period'])->get('id')[0];
                    return self::getSchoolPeriodById($schoolPeriod['id'],$request);
                }else{
                    return response()->json(['message'=>'Materia o profesor invalida'],206);
                }
            }else{
                SchoolPeriod::create($request->all());
                $schoolPeriod = SchoolPeriod::where('cod_school_period',$request['cod_school_period'])->get('id')[0];
                return self::getSchoolPeriodById($schoolPeriod['id'],$request);
            }
        }
    }
}