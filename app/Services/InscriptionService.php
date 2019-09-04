<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 30/08/19
 * Time: 09:08 AM
 */

namespace App\Services;

use App\User;
use Illuminate\Http\Request;
use App\SchoolPeriodStudent;
use App\Student;
use App\SchoolPeriod;
use App\SchoolPeriodSubjectTeacher;
use App\StudentSubject;

class InscriptionService
{
    public static function clearInscription($inscriptions){
        $inscriptionsReturn=[];
        foreach ($inscriptions as $inscription){
            unset($inscription['schoolPeriod']);
            $inscriptionsReturn[]=$inscription;
        }
        return $inscriptionsReturn;
    }

    public static function getInscription(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $inscriptions = SchoolPeriodStudent::getSchoolPeriodStudent($organizationId);
        if (count($inscriptions)>0){
            return self::clearInscription($inscriptions);
        }
        return response()->json(['message'=>'No existen inscripciones'],206);
    }

    public static function getInscriptionById(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $inscription = SchoolPeriodStudent::getSchoolPeriodStudentById($id,$organizationId);
        //var_dump($inscription);
        if (count($inscription)>0){
            return self::clearInscription($inscription)[0];
        }
        return response()->json(['Inscripcion no encontrada'],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'student_id'=>'required',
            'school_period_id'=>'required',
            'status'=>'max:5|ends_with:RET-A,RET-B,DES-A,DES-B,INC-A,INC-B,REI-A,REI-B,REG',
            'subjects.*.school_period_subject_teacher_id'=>'required',
            'subjects.*.status'=>'max:3|ends_with:CUR,RET,APR,REP',
        ]);
    }

    public static function validateRelation(Request $request)
    {
        $organizationId = $request->header('organization_key');
        if (!Student::existStudent($request['student_id'],$organizationId)){
            return false;
        }else{
            $student = Student::getStudent($request['student_id']);
            if (count(User::getUserById($student[0]['user_id'],'S',$organizationId))<=0){
                return false;
            }
        }
        if (!SchoolPeriod::existSchoolPeriodById($request['school_period_id'],$organizationId)){
            return false;
        }
        foreach ($request['subjects'] as $subject){
            if (!SchoolPeriodSubjectTeacher::existSchoolPeriodSubjectTeacherById($subject['school_period_subject_teacher_id'])){
                return false;
            }else{
                $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacher($subject['school_period_subject_teacher_id'])[0];
                if ($schoolPeriodSubjectTeacher['school_period_id']!=$request['school_period_id']){
                    return false;
                }
            }
        }
        return true;
    }


    public static function addSubjects($subjects,$schoolPeriodStudent)
    {
        foreach ($subjects as $subject){
            StudentSubject::addStudentSubject([
                'school_period_student_id'=>$schoolPeriodStudent,
                'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                'qualification'=>0,
                'status'=>'CUR'
            ]);
            SchoolPeriodSubjectTeacher::updateEnrolledStudent($subject['school_period_subject_teacher_id']);
        }
    }
    public static function addInscription(Request $request)
    {
        self::validate($request);
        if(self::validateRelation($request)){
            if (!SchoolPeriodStudent::existSchoolPeriodStudent($request['student_id'],$request['school_period_id'])){
                $request['status']='INC-A';
                SchoolPeriodStudent::addSchoolPeriodStudent($request);
                $schoolPeriodStudentId= SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],$request['school_period_id'])[0]['id'];
                self::addSubjects($request['subjects'],$schoolPeriodStudentId);
                return self::getInscriptionById($request,$schoolPeriodStudentId);
            }
            return response()->json(['message'=>'Inscripcion ya realizada'],206);
        }
        return response()->json(['message'=>'Relacion invalida'],206);
    }

    public static function deleteInscription(Request $request,$id)
    {
        $organizationId = $request->header('organization_key');
        if(SchoolPeriodStudent::existSchoolPeriodStudentById($id,$organizationId)){
            SchoolPeriodStudent::deleteSchoolPeriodStudent($id);
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Inscripcion no encontrada'],206);
    }

    public static function updateSubjects($subjects,$schoolPeriodStudentId)
    {
        $subjectsInBd=StudentSubject::findStudentSubjectBySchoolPeriodStudent($schoolPeriodStudentId);
        $subjectsUpdated=[];
        foreach ($subjects as $subject){
            $existSubject = false;
            foreach ($subjectsInBd as $subjectInBd){
                if ($subject['school_period_subject_teacher_id']==$subjectInBd['school_period_subject_teacher_id']){
                    $subject['qualification']=$subjectInBd['qualification'];
                    $subject['school_period_student_id']=$schoolPeriodStudentId;
                    StudentSubject::updateStudentSubject($subjectInBd['id'],$subject);
                    $subjectsUpdated[]=$subjectInBd['id'];
                    $existSubject=true;
                    break;
                }
            }
            if ($existSubject==false){
                self::addSubjects([$subject],$schoolPeriodStudentId);
                $subjectsUpdated[]=StudentSubject::findSchoolPeriodStudent($schoolPeriodStudentId,$subject['school_period_subject_teacher_id'])[0]['id'];
            }
        }
        foreach ($subjectsInBd as $subjectId){
            if (!in_array($subjectId['id'],$subjectsUpdated)){
                StudentSubject::deleteStudentSubject($subjectId['id']);
            }
        }
    }

    public static function updateInscription(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        self::validate($request);
        if (self::validateRelation($request)){
            if (SchoolPeriodStudent::existSchoolPeriodStudentById($id,$organizationId)){
                if (SchoolPeriodStudent::existSchoolPeriodStudent($request['student_id'],$request['school_period_id'])){
                    if( SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],$request['school_period_id'])[0]['id']!=$id){
                        return response()->json(['message'=>'Inscripcion ocupada'],206);
                    }
                }
                SchoolPeriodStudent::updateSchoolPeriodStudent($id,$request);
                self::updateSubjects($request['subjects'],$id);
                return self::getInscriptionById($request,$id);
            }
            return response()->json(['message'=>'Inscripcion no encontrada'],206);
        }
        return response()->json(['message'=>'Relacion invalida'],206);
    }
}