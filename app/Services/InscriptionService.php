<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 30/08/19
 * Time: 09:08 AM
 */

namespace App\Services;

use App\Postgraduate;
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
            return ($inscriptions);
        }
        return response()->json(['message'=>'No existen inscripciones'],206);
    }

    public static function getInscriptionById(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $inscription = SchoolPeriodStudent::getSchoolPeriodStudentById($id,$organizationId);
        //var_dump($inscription);
        if (count($inscription)>0){
            return ($inscription)[0];
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

    public static function existSubjectInAvailableSubjects($subjectId,$availableSubjects){
        foreach ($availableSubjects as $availableSubject){
            if ($availableSubject['id'] == $subjectId ){
                return true;
            }
        }
        return false;
    }

    public static function isValidSubjects($subjects,$availableSubjects)
    {
        if (!is_array($availableSubjects)){
            return false;
        }
        foreach($subjects as $subject){
            if (!self::existSubjectInAvailableSubjects($subject['school_period_subject_teacher_id'],$availableSubjects)){
                return false;
            }
        }
        return true;
    }

    public static function getCurrentAmountCredits($studentId)
    {
        $currentAmountCredits = 0;
        $approvedSubjects = StudentSubject::getApprovedSubjects($studentId);
        if (count($approvedSubjects)>0){
            foreach ($approvedSubjects as $approvedSubject){
                $currentAmountCredits += $approvedSubject['subject']['uc'];
            }
        }
        return $currentAmountCredits;
    }

    public static function getTotalAmountCredits($studentId,$organizationId)
    {
        $postgraduateId = Student::getStudent($studentId)[0]['postgraduate_id'];

        return Postgraduate::getPostgraduateById($postgraduateId,$organizationId);
    }

    public static function getNumberCreditsInscription($subjects)
    {
        $numberCreditsInscription = 0;
        foreach ($subjects as $subject){
            $numberCreditsInscription += SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherById($subject['school_period_subject_teacher_id'])[0]['subject']['uc'];
        }
        return $numberCreditsInscription;
    }

    public static function isValidCredits($studentId,$subjects,$organizationId)
    {
        $currentAmountCredits = self::getCurrentAmountCredits($studentId);
        $totalAmountCredits = self::getTotalAmountCredits($studentId,$organizationId);
        $numberCreditsInscription = self::getNumberCreditsInscription($subjects);
        if ($currentAmountCredits+$numberCreditsInscription > $totalAmountCredits){
            return false;
        }
        return true;
    }

    public static function addInscription(Request $request)
    {
        $organizationId = $request->header('organization_key');
        self::validate($request);
        if(self::validateRelation($request)){
            if (self::isValidSubjects($request['subjects'],self::getAvailableSubjects($request['student_id'],$request['school_period_id'],$request))){
                if (self::isValidCredits($request['student_id'],$request['subjects'],$organizationId)){
                    if (!SchoolPeriodStudent::existSchoolPeriodStudent($request['student_id'],$request['school_period_id'])){
                        $request['status']='INC-A';
                        SchoolPeriodStudent::addSchoolPeriodStudent($request);
                        $schoolPeriodStudentId= SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],$request['school_period_id'])[0]['id'];
                        self::addSubjects($request['subjects'],$schoolPeriodStudentId);
                        return self::getInscriptionById($request,$schoolPeriodStudentId);
                    }
                    return response()->json(['message'=>'Inscripcion ya realizada'],206);
                }
                return response()->json(['message'=>'La cantidad de creditos inscritos excede el limite permitido en su postgrado'],206);
            }
            return response()->json(['message'=>'Alguna de las materias que intenta inscribir no esta disponible porque ya la aprobo o esta al limite de estudiantes'],206);
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
            if (self::isValidSubjects($request['subjects'],self::getAvailableSubjects($request['student_id'],$request['school_period_id'],$request))){
                if (self::isValidCredits($request['student_id'],$request['subjects'],$organizationId)){
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
                return response()->json(['message'=>'La cantidad de creditos inscritos excede el limite permitido en su postgrado'],206);
            }
            return response()->json(['message'=>'Alguna de las materias que intenta inscribir no esta disponible porque ya la aprobo o esta al limite de estudiantes'],206);
        }
        return response()->json(['message'=>'Relacion invalida'],206);
    }

    public static function isApprovedSubject($subjectId,$approvedSubjects)
    {
        foreach ($approvedSubjects as $approvedSubject){
            if ($approvedSubject['subject']['subject_id']==$subjectId){
                return true;
            }
        }
        return false;
    }

    public static function getAvailableSubjects($studentId,$schoolPeriodId,Request $request)
    {
        $subjectsInSchoolPeriod = SchoolPeriodSubjectTeacher::findSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId);
        if (count($subjectsInSchoolPeriod)>0){
            $approvedSubjects = StudentSubject::getApprovedSubjects($studentId);
            $availableSubjects=[];
            foreach ($subjectsInSchoolPeriod as $subjectInSchoolPeriod){
                if ($subjectInSchoolPeriod['enrolled_students']<$subjectInSchoolPeriod['limit']){
                    if (count($approvedSubjects)>0){
                        if (!self::isApprovedSubject($subjectInSchoolPeriod['subject_id'],$approvedSubjects)){
                            $availableSubjects[]=$subjectInSchoolPeriod;
                        }
                    }else{
                       $availableSubjects[]=$subjectInSchoolPeriod;
                    }
                }
            }
            if (count($availableSubjects)>0){
                return $availableSubjects;
            }
        }
        return response()->json(['message'=>'No hay materias disponibles para inscribir'],206);
    }
}