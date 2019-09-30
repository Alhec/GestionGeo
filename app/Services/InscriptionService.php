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
use phpDocumentor\Reflection\Types\Self_;

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
            'student_id'=>'required|numeric',
            'school_period_id'=>'required|numeric',
            'status'=>'required|max:5|ends_with:RET-A,RET-B,DES-A,DES-B,INC-A,INC-B,REI-A,REI-B,REG',
            'subjects.*.school_period_subject_teacher_id'=>'required|numeric',
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

    public static function prepareArrayOfSubject($subject,$schoolPeriodStudentId,$isWithdrawn)
    {
        if (isset($subject['qualification'])){
            if ($subject['qualification']>=10){
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'qualification'=>$subject['qualification'],
                    'status'=>'APR'
                ];
            }else{
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'qualification'=>$subject['qualification'],
                    'status'=>'REP'
                ];
            }
        }else{
            if ($isWithdrawn){
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'status'=>'RET'
                ];
            }else{
                if (isset($subject['status'])){
                    $studentSubject=[
                        'school_period_student_id'=>$schoolPeriodStudentId,
                        'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                        'status'=>$subject['status']
                    ];
                }else{
                    $studentSubject=[
                        'school_period_student_id'=>$schoolPeriodStudentId,
                        'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                        'status'=>'CUR'
                    ];
                }
            }
        }
        return $studentSubject;
    }

    public static function addSubjects($subjects,$schoolPeriodStudentId,$isWithdrawn)
    {
        foreach ($subjects as $subject){
            $studentSubject = self::prepareArrayOfSubject($subject,$schoolPeriodStudentId,$isWithdrawn);
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
        if ($currentAmountCredits+$numberCreditsInscription >= $totalAmountCredits){
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
                return response()->json(['message'=>'Los creditos exceden el limite de los credistos disponibles para tu postgrado'],206);
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

    public static function validateRelationUpdate($organizationId,Request $request)
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
        $subjectsEnrolledInSchoolPeriod = SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],$request['school_period_id'])[0]['enrolledSubjects'];
        if (count($availableSubjects)<=0 && count($subjectsEnrolledInSchoolPeriod)<=0){
            return false;
        }
        $availableSubjectsId=[];
        foreach($availableSubjects as $availableSubject){
            $availableSubjectsId[]=$availableSubject['id'];
        }
        foreach ($subjectsEnrolledInSchoolPeriod as $subjectEnrolledInSchoolPeriod){
            if (!in_array($subjectEnrolledInSchoolPeriod['school_period_subject_teacher_id'],$availableSubjectsId)){
                $availableSubjectsId[]=$subjectEnrolledInSchoolPeriod['school_period_subject_teacher_id'];
            }
        }
        foreach ($request['subjects'] as $subject){
            if (!in_array($subject['school_period_subject_teacher_id'],$availableSubjectsId)){
                dd(1);
                return false;
            }
        }
        return true;
    }

    public static function updateStatus($schoolPeriodStudentId,$organizationId)
    {
        $schoolPeriodStudent = SchoolPeriodStudent::getSchoolPeriodStudentById($schoolPeriodStudentId,$organizationId)[0];
        $enrolledSubjects = $schoolPeriodStudent['enrolledSubjects'];
        $allWithdrawn=true;
        foreach ($enrolledSubjects as $enrolledSubject){
            if ($enrolledSubject['status']!='RET'){
                $allWithdrawn = false;
                break;
            }
        }
        if ($allWithdrawn){
            $schoolPeriodStudent['status']='RET-A';
            SchoolPeriodStudent::updateSchoolPeriodStudentLikeArray($schoolPeriodStudentId,
                ['student_id'=>$schoolPeriodStudent['student_id'],
                    'school_period_id'=>$schoolPeriodStudent['school_period_id'],
                    'pay_ref'=>$schoolPeriodStudent['pay_ref'],
                    'status'=>$schoolPeriodStudent['status']
                ]);
        }else{
            if ($schoolPeriodStudent['status']=='RET-A'||$schoolPeriodStudent['status']=='RET-B'){
                $schoolPeriodStudent['status']='REG';
                SchoolPeriodStudent::updateSchoolPeriodStudentLikeArray($schoolPeriodStudentId,
                    ['student_id'=>$schoolPeriodStudent['student_id'],
                        'school_period_id'=>$schoolPeriodStudent['school_period_id'],
                        'pay_ref'=>$schoolPeriodStudent['pay_ref'],
                        'status'=>$schoolPeriodStudent['status']
                    ]);
            }
        }
    }

    public static function updateSubjects($subjects,$schoolPeriodStudentId,$organizationId,$isWithdrawn)
    {
        $subjectsInBd=StudentSubject::studentSubjectBySchoolPeriodStudent($schoolPeriodStudentId);
        $subjectsUpdated=[];
        foreach ($subjects as $subject){
            $existSubject = false;
            foreach ($subjectsInBd as $subjectInBd){
                if ($subject['school_period_subject_teacher_id']==$subjectInBd['school_period_subject_teacher_id']){
                    $studentSubject = self::prepareArrayOfSubject($subject,$schoolPeriodStudentId,$isWithdrawn);
                    StudentSubject::updateStudentSubject($subjectInBd['id'],$studentSubject);
                    $subjectsUpdated[]=$subjectInBd['id'];
                    $existSubject=true;
                    break;
                }
            }
            if ($existSubject==false){
                self::addSubjects([$subject],$schoolPeriodStudentId,$isWithdrawn);
                $subjectsUpdated[]=StudentSubject::findSchoolPeriodStudentId($schoolPeriodStudentId,$subject['school_period_subject_teacher_id'])[0]['id'];
            }
        }
        foreach ($subjectsInBd as $subjectId){
            if (!in_array($subjectId['id'],$subjectsUpdated)){
                StudentSubject::deleteStudentSubject($subjectId['id']);
            }
        }
        self::updateStatus($schoolPeriodStudentId,$organizationId);
    }

    public static function updateInscription(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        self::validate($request);
        if (SchoolPeriodStudent::existSchoolPeriodStudentById($id,$organizationId)) {
            $schoolPeriodStudentIdInBd= SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],$request['school_period_id']);
            if(count($schoolPeriodStudentIdInBd)>0){
                if ($schoolPeriodStudentIdInBd[0]['id']!=$id){
                    return response()->json(['message'=>'El estudiante ya esta inscrito en el periodo escolar y se encuentra en otro registro'],206);
                }
            }
            if(self::validateRelationUpdate($organizationId,$request)){
                if (self::isValidCredits($request['student_id'],$request['school_period_id'],$request['subjects'],$organizationId)){
                    SchoolPeriodStudent::updateSchoolPeriodStudent($id,$request);
                    if($request['status']=='RET-A'||$request['status']=='RET-B'){
                        self::updateSubjects($request['subjects'],$id,$organizationId,true);
                    }else{
                        self::updateSubjects($request['subjects'],$id,$organizationId,false);
                    }
                    return self::getInscriptionById($request,$id);
                }
                return response()->json(['message'=>'Los creditos exceden el limite de los credistos disponibles para tu postgrado'],206);
            }
            return response()->json(['message'=>'Relacion invalida'],206);
        }
        return response()->json(['message'=>'Inscripcion no encontrada'],206);
    }

    public static function studentAvailableSubjects(Request $request)
    {

    }

    public static function studentInscription()
    {
        //dd(auth()->user()['id']);
    }
}
