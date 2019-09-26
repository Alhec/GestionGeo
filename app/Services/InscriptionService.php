<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 30/08/19
 * Time: 09:08 AM
 */

namespace App\Services;

use App\Postgraduate;
use App\Subject;
use App\User;
use Illuminate\Http\Request;
use App\SchoolPeriodStudent;
use App\Student;
use App\SchoolPeriod;
use App\SchoolPeriodSubjectTeacher;
use App\StudentSubject;

class InscriptionService
{
    public static function getInscriptions(Request $request)
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
        if (count($inscription)>0){
            return ($inscription)[0];
        }
        return response()->json(['message'=>'Inscripcion no encontrada'],206);
    }

    public static function getInscriptionsBySchoolPeriod(Request $request, $schoolPeriodId)
    {
        $organizationId = $request->header('organization_key');
        $inscription = SchoolPeriodStudent::getSchoolPeriodStudentBySchoolPeriod($schoolPeriodId,$organizationId);
        if (count($inscription)>0){
            return ($inscription);
        }
        return response()->json(['message'=>'Periodo escolar no posee inscripciones'],206);
    }

    public static function isApprovedSubject($subjectId,$approvedSubjects)
    {
        foreach ($approvedSubjects as $approvedSubject){
            if ($approvedSubject['dataSubject']['subject_id']==$subjectId){
                return true;
            }
        }
        return false;
    }

    public static function getSubjectsNotYetApproved($studentId,$subjectsInSchoolPeriod)
    {
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
        return $availableSubjects;
    }

    public static function filterSubjectsByPostgraduate($student,$organizationId,$availableSubjects)
    {
        $subjectsInPostgraduate = Subject::getSubjectsByPostgraduate($student['postgraduate_id'],$organizationId);
        $availableSubjectsInPostgraduate=[];
        foreach ($availableSubjects as $availableSubject){
            foreach ($subjectsInPostgraduate as $subjectInPostgraduate){
                if ($availableSubject['subject_id']==$subjectInPostgraduate['id']){
                    $availableSubjectsInPostgraduate[]=$availableSubject;
                    break;
                }
            }
        }
        return $availableSubjectsInPostgraduate;
    }

    public static function filterSubjectsEnrolledInSchoolPeriod($studentId,$schoolPeriodId,$availableSubjects)
    {
        $enrolledSubjects = StudentSubject::getEnrolledSubjectsBySchoolPeriod($studentId,$schoolPeriodId);
        if (count($enrolledSubjects)>0){
            $filterSubjectsEnrolled = [];
            foreach ($availableSubjects as $availableSubject){
                $subjectFound=false;
                foreach ($enrolledSubjects as $enrolledSubject){
                    if ($availableSubject['subject_id']==$enrolledSubject['dataSubject']['subject_id']){
                        $subjectFound = true;
                        break;
                    }
                }
                if ($subjectFound ==false){
                    $filterSubjectsEnrolled[]=$availableSubject;
                }
            }
            return $filterSubjectsEnrolled;
        }
        return $availableSubjects;
    }

    public static function getAvailableSubjects($studentId,$schoolPeriodId,Request $request,$internalCall)
    {
        $organizationId = $request->header('organization_key');
        $student= Student::getStudentById($studentId)[0];
        if (User::existUserById($student['user_id'],'S',$organizationId)){
            $subjectsInSchoolPeriod = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId);
            if (count($subjectsInSchoolPeriod)>0 && (self::getCurrentAmountCredits($studentId)<self::getTotalAmountCredits($studentId,$organizationId))){
                $subjectsNotYetApproved = self::getSubjectsNotYetApproved($studentId,$subjectsInSchoolPeriod);
                if (count($subjectsNotYetApproved)>0){
                    $filterSubjectsByPostgraduate = self::filterSubjectsByPostgraduate($student,$organizationId,$subjectsNotYetApproved);
                    if (count($filterSubjectsByPostgraduate)>0){
                        $availableSubjects= self::filterSubjectsEnrolledInSchoolPeriod($studentId,$schoolPeriodId,$filterSubjectsByPostgraduate);
                        if (count($availableSubjects)>0){
                            return $availableSubjects;
                        }
                    }
                }
            }
            if ($internalCall){
                return [];
            }
            return response()->json(['message'=>'No hay materias disponibles para inscribir'],206);
        }
        return response()->json(['message'=>'No existe el estudiante dado el id'],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'student_id'=>'required',
            'school_period_id'=>'required',
            'status'=>'required|max:5|ends_with:RET-A,RET-B,DES-A,DES-B,INC-A,INC-B,REI-A,REI-B,REG',
            'subjects.*.school_period_subject_teacher_id'=>'required',
            'subjects.*.status'=>'max:3|ends_with:CUR,RET,APR,REP',
            'subjects.*.qualification'=>'numeric'
        ]);
    }

    public static function validateRelation($organizationId,Request $request)
    {
        if (Student::existStudentByid($request['student_id'])){
            $student= Student::getStudentById($request['student_id']);
            if (!User::existUserById($student[0]['user_id'],'S',$organizationId)) {
                return false;
            }
        }else{
            return false;
        }
        if (!SchoolPeriod::existSchoolPeriodById($request['school_period_id'],$organizationId)){
            return false;
        }
        $availableSubjects=self::getAvailableSubjects($request['student_id'],$request['school_period_id'],$request,true);
        if (count($availableSubjects)<=0){
            return false;
        }
        $availableSubjectsId=[];
        foreach($availableSubjects as $availableSubject){
            $availableSubjectsId[]=$availableSubject['id'];
        }
        foreach ($request['subjects'] as $subject){
            if (!in_array($subject['school_period_subject_teacher_id'],$availableSubjectsId)){
                return false;
            }
        }
        return true;
    }

    public static function addSubjects($subjects,$schoolPeriodStudentId,$isWithdrawn)
    {
        foreach ($subjects as $subject){

            if (isset($subject['qualification'])&& $isWithdrawn){
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'qualification'=>$subject['qualification'],
                    'status'=>'RET'
                ];
            }else if (isset($subject['qualification'])&& !$isWithdrawn){
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'qualification'=>$subject['qualification'],
                    'status'=>'CUR'
                ];
            }else if (!isset($subject['qualification'])&& $isWithdrawn){
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'status'=>'RET'
                ];
            }else{
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'status'=>'CUR'
                ];
            }
            StudentSubject::addStudentSubject($studentSubject);
            SchoolPeriodSubjectTeacher::updateEnrolledStudent($subject['school_period_subject_teacher_id']);
        }
    }

    public static function getCurrentAmountCredits($studentId)
    {
        $currentAmountCredits = 0;
        $approvedSubjects = StudentSubject::getApprovedSubjects($studentId);
        if (count($approvedSubjects)>0){
            foreach ($approvedSubjects as $approvedSubject){
                $currentAmountCredits += $approvedSubject['dataSubject']['subject']['uc'];
            }
        }
        return $currentAmountCredits;
    }

    public static function getTotalAmountCredits($studentId,$organizationId)
    {
        $postgraduateId = Student::getStudentById($studentId)[0]['postgraduate_id'];
        return Postgraduate::getPostgraduateById($postgraduateId,$organizationId)[0]['num_cu'];
    }

    public static function getNumberCreditsInscription($schoolPeriodId,$subjects)
    {
        $subjectsInSchoolPeriod = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId);
        $numberCreditsInscription = 0;
        foreach ($subjects as $subject){
            foreach ($subjectsInSchoolPeriod as $subjectInSchoolPeriod){
                if ($subject['school_period_subject_teacher_id']==$subjectInSchoolPeriod['id']){
                    $numberCreditsInscription += $subjectInSchoolPeriod['subject']['uc'];
                }
            }
        }
        return $numberCreditsInscription;
    }

    public static function isValidCredits($studentId,$schoolPeriodId,$subjects,$organizationId)
    {
        $numberCreditsInscription = self::getNumberCreditsInscription($schoolPeriodId,$subjects);
        $currentAmountCredits = self::getCurrentAmountCredits($studentId);
        $totalAmountCredits = self::getTotalAmountCredits($studentId,$organizationId);
        if ($currentAmountCredits+$numberCreditsInscription >= $totalAmountCredits || $numberCreditsInscription>15){
            dd(5);
            return false;
        }
        return true;
    }

    public static function addInscription(Request $request)
    {
        $organizationId = $request->header('organization_key');
        self::validate($request);
        if (!SchoolPeriodStudent::existSchoolPeriodStudent($request['student_id'],$request['school_period_id'])) {
            if(self::validateRelation($organizationId,$request)){
                if (self::isValidCredits($request['student_id'],$request['school_period_id'],$request['subjects'],$organizationId)){
                    $schoolPeriodStudentId=SchoolPeriodStudent::addSchoolPeriodStudent($request);
                    if($request['status']=='RET-A'||$request['status']=='RET-B'){
                        self::addSubjects($request['subjects'],$schoolPeriodStudentId,true);
                    }else{
                        self::addSubjects($request['subjects'],$schoolPeriodStudentId,false);
                    }
                    return self::getInscriptionById($request,$schoolPeriodStudentId);
                }
                return response()->json(['message'=>'La cantidad de creditos inscritos excede el limite de 15 creditos'],206);
            }
            return response()->json(['message'=>'Relacion invalida'],206);
        }
        return response()->json(['message'=>'Inscripcion ya realizada'],206);
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
            if (self::isValidSubjects($request['subjects'],self::getAvailableSubjects($request['student_id'],$request['school_period_id'],$request,true))){
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




}
