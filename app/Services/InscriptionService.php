<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 30/08/19
 * Time: 09:08 AM
 */

namespace App\Services;

use App\SchoolProgram;
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

    const taskError = 'No se puede proceder con la tarea';
    const emptyInscriptions ='No existen inscripciones';
    const notFoundInscription = 'Inscripcion no encontrada';
    const emptyInscriptionInCurrentSchoolPeriod = 'Periodo escolar no posee inscripciones';
    const OK = 'OK';
    const nontFoundStudentGivenId = 'No existe el estudiante dado el id';

    public static function getInscriptions(Request $request,$organizationId)
    {
        $inscriptions = SchoolPeriodStudent::getSchoolPeriodStudent($organizationId);
        if (is_numeric($inscriptions)&&$inscriptions==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($inscriptions)>0){
            return ($inscriptions);
        }
        return response()->json(['message'=>self::emptyInscriptions],206);
    }

    public static function getInscriptionById(Request $request, $id,$organizationId)
    {
        $inscription = SchoolPeriodStudent::getSchoolPeriodStudentById($id,$organizationId);
        if (is_numeric($inscription)&&$inscription==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($inscription)>0){
            return ($inscription)[0];
        }
        return response()->json(['message'=>self::notFoundInscription],206);
    }

    public static function getInscriptionsBySchoolPeriod(Request $request, $schoolPeriodId,$organizationId)
    {
        $inscriptions = SchoolPeriodStudent::getSchoolPeriodStudentBySchoolPeriod($schoolPeriodId,$organizationId);
        if (is_numeric($inscriptions)&&$inscriptions==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($inscriptions)>0){
            return ($inscriptions);
        }
        return response()->json(['message'=>self::emptyInscriptionInCurrentSchoolPeriod],206);
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

    public static function getUnregisteredSubjects($studentId,$subjectsInSchoolPeriod)
    {
        $allSubjectsEnrolled = StudentSubject::getAllSubjectsEnrolled($studentId);
        $availableSubjects=[];
        foreach ($subjectsInSchoolPeriod as $subjectInSchoolPeriod){
            if ($subjectInSchoolPeriod['enrolled_students']<$subjectInSchoolPeriod['limit']){
                if (count($allSubjectsEnrolled)>0){
                    if (!self::isApprovedSubject($subjectInSchoolPeriod['subject_id'],$allSubjectsEnrolled)){
                        $availableSubjects[]=$subjectInSchoolPeriod;
                    }
                }else{
                    $availableSubjects[]=$subjectInSchoolPeriod;
                }
            }
        }
        return $availableSubjects;
    }

    public static function filterSubjectsBySchoolProgram($student, $organizationId, $availableSubjects)
    {
        $subjectsInSchoolProgram = Subject::getSubjectsBySchoolProgram($student['school_program_id'],$organizationId);
        $availableSubjectsInSchoolProgram=[];
        foreach ($availableSubjects as $availableSubject){
            foreach ($subjectsInSchoolProgram as $subjectInSchoolProgram){
                if ($availableSubject['subject_id']==$subjectInSchoolProgram['id']){
                    $availableSubjectsInSchoolProgram[]=$availableSubject;
                    break;
                }
            }
        }
        return $availableSubjectsInSchoolProgram;
    }

    public static function filterSubjectsEnrolledInSchoolPeriod($studentId,$schoolPeriodId,$availableSubjects)
    {
        $enrolledSubjects = StudentSubject::getEnrolledSubjectsBySchoolPeriodStudent($studentId,$schoolPeriodId);
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

    public static function getAvailableSubjects($studentId,$schoolPeriodId,Request $request,$organizationId,$internalCall)
    {
        $student= Student::getStudentById($studentId);
        if (is_numeric($student)&&$student==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($student)>0){
            $student=$student[0];
            $existUserById = User::existUserById($student['user_id'],'S',$organizationId);
            if (is_numeric($existUserById)&&$existUserById==0){
                return response()->json(['message' => self::taskError], 206);
            }
            if ($existUserById){
                $subjectsInSchoolPeriod = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId);
                if (is_numeric($subjectsInSchoolPeriod)&&$subjectsInSchoolPeriod==0){
                    return response()->json(['message' => self::taskError], 206);
                }
                if (count($subjectsInSchoolPeriod)>0){
                    $subjectsNotYetApproved = self::getUnregisteredSubjects($studentId,$subjectsInSchoolPeriod);
                    if (count($subjectsNotYetApproved)>0){
                        $filterSubjectsBySchoolProgram = self::filterSubjectsBySchoolProgram($student,$organizationId,$subjectsNotYetApproved);
                        if (count($filterSubjectsBySchoolProgram)>0){
                            $availableSubjects= self::filterSubjectsEnrolledInSchoolPeriod($studentId,$schoolPeriodId,$filterSubjectsBySchoolProgram);
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
        }
        return response()->json(['message'=>self::nontFoundStudentGivenId],206);
    }

    public static function validate(Request $request)
    {

        $request->validate([
            'student_id'=>'required|numeric',
            'school_period_id'=>'required|numeric',
            'status'=>'required|max:5|ends_with:RET-A,RET-B,DES-A,DES-B,INC-A,INC-B,REI-A,REI-B,REG',
            'pay_ref'=>'max:50',
            'financing'=>'max:3|ends_with:EXO,SFI,SCS,FUN',//EXO exonerated, FUN Funded, SFI Self-financing, ScS Scholarship
            'amount_paid'=>'numeric',
            'financing_description'=>'max:60',
            'subjects.*.school_period_subject_teacher_id'=>'required|numeric',
            'subjects.*.status'=>'max:3|ends_with:CUR,RET,APR,REP',
            'subjects.*.qualification'=>'numeric'
        ]);
    }

    public static function validateRelation($organizationId,Request $request)
    {
        if (Student::existStudentById($request['student_id'])){
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
        $approvedSubjects = StudentSubject::getAllSubjectsEnrolled($studentId);
        if (count($approvedSubjects)>0){
            foreach ($approvedSubjects as $approvedSubject){
                $currentAmountCredits += $approvedSubject['dataSubject']['subject']['uc'];
            }
        }
        return $currentAmountCredits;
    }

    public static function getTotalAmountCredits($studentId,$organizationId)
    {
        $schoolProgramId = Student::getStudentById($studentId)[0]['school_program_id'];
        return SchoolProgram::getSchoolProgramById($schoolProgramId,$organizationId)[0]['num_cu'];
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
        if ($currentAmountCredits+$numberCreditsInscription > $totalAmountCredits){
            return false;
        }
        return true;
    }

    public static function addInscription(Request $request,$organizationId)
    {

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

    public static function deleteInscription(Request $request,$id,$organizationId)
    {
        $existSchoolPeriodStudentById=SchoolPeriodStudent::existSchoolPeriodStudentById($id,$organizationId);
        if (is_numeric($existSchoolPeriodStudentById) && $existSchoolPeriodStudentById==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if($existSchoolPeriodStudentById){
            $result=SchoolPeriodStudent::deleteSchoolPeriodStudent($id);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message' => self::taskError], 206);
            }
            return response()->json(['message'=>self::OK]);
        }
        return response()->json(['message'=>self::notFoundInscription],206);
    }

    public static function validateRelationUpdate($organizationId,Request $request)
    {
        if (Student::existStudentById($request['student_id'])){
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
                    'status'=>$schoolPeriodStudent['status'],
                    'financing'=>$schoolPeriodStudent['financing'],
                    'financing_description'=>$schoolPeriodStudent['financing_description'],
                    'amount_paid'=>$schoolPeriodStudent['amount_paid'],
                ]);
        }else{
            if ($schoolPeriodStudent['status']=='RET-A'||$schoolPeriodStudent['status']=='RET-B'){
                $schoolPeriodStudent['status']='REG';
                SchoolPeriodStudent::updateSchoolPeriodStudentLikeArray($schoolPeriodStudentId,
                    ['student_id'=>$schoolPeriodStudent['student_id'],
                        'school_period_id'=>$schoolPeriodStudent['school_period_id'],
                        'pay_ref'=>$schoolPeriodStudent['pay_ref'],
                        'status'=>$schoolPeriodStudent['status'],
                        'financing'=>$schoolPeriodStudent['financing'],
                        'financing_description'=>$schoolPeriodStudent['financing_description'],
                        'amount_paid'=>$schoolPeriodStudent['amount_paid'],
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

    public static function studentAvailableSubjects($studentId,Request $request,$organizationId)
    {
        $request['student_id']=$studentId;
        $isValid=StudentService::validateStudent($request);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (count($currentSchoolPeriod)>0){
                if ($currentSchoolPeriod[0]['inscription_visible']==true){
                    return self::getAvailableSubjects($studentId,$currentSchoolPeriod[0]['id'],$request,false);
                }
                return response()->json(['message'=>'No estan disponibles las inscripciones'],206);
            }
            return response()->json(['message'=>'No hay periodo escolar en curso'],206);
        }
        return $isValid;
    }

    public static function studentAddInscription(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $isValid=StudentService::validateStudent($request);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (count($currentSchoolPeriod)>0){
                if ($currentSchoolPeriod[0]['inscription_visible']==true){
                    if (self::getNumberCreditsInscription($currentSchoolPeriod[0]['id'],$request['subjects'])<=15){
                        $request['school_period_id']=$currentSchoolPeriod[0]['id'];
                        return self::addInscription($request);
                    }
                    return response()->json(['message'=>'Los creditos exceden el limite de los credistos disponibles para tu postgrado'],206);
                }
                return response()->json(['message'=>'No estan disponibles las inscripciones'],206);
            }
            return response()->json(['message'=>'No hay periodo escolar en curso'],206);
        }
        return $isValid;
    }

    public static function getCurrentEnrolledSubjects($studentId,Request $request){
        $organizationId = $request->header('organization_key');
        $request['student_id']=$studentId;
        $isValid=StudentService::validateStudent($request);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (count($currentSchoolPeriod)>0){
                $inscription = SchoolPeriodStudent::findSchoolPeriodStudent($studentId,$currentSchoolPeriod[0]['id']);
                if (count($inscription)>0){
                    return $inscription[0];
                }
                return response()->json(['message'=>'No hay inscripcion actual para usted'],206);
            }
            return response()->json(['message'=>'No hay periodo escolar en curso'],206);
        }
        return $isValid;
    }

    public static function validateWithdrawSubjects($withdrawSubjects,$enrolledSubjects)
    {
        foreach ($withdrawSubjects as $withdrawSubject){
            $found = false;
            foreach ($enrolledSubjects as $enrolledSubject){
                if ($withdrawSubject['student_subject_id']==$enrolledSubject['id']){
                    $found=true;
                    break;
                }
            }
            if ($found ==false){
                return false;
            }
        }
        return true;
    }

    public static function changeStatusSubjects($schoolPeriodStudentId,$organizationId,$withdrawSubjects)
    {
        foreach ($withdrawSubjects as $withdrawSubject){
            $studentSubject = StudentSubject::getStudentSubjectById($withdrawSubject['student_subject_id'])[0];
            StudentSubject::updateStudentSubject($withdrawSubject['student_subject_id'],[
                "school_period_student_id"=>$studentSubject['school_period_student_id'],
                "school_period_subject_teacher_id"=>$studentSubject['school_period_subject_teacher_id'],
                "qualification"=>$studentSubject['qualification'],
                "status"=>'RET'
            ]);
            self::updateStatus($schoolPeriodStudentId,$organizationId);
        }
    }

    public static function withdrawSubjects(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $isValid=StudentService::validateStudent($request);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (count($currentSchoolPeriod)>0){
                if (strtotime($currentSchoolPeriod[0]['withdrawal_deadline'])>=strtotime(now()->toDateTimeString())){
                    $inscription = SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],$currentSchoolPeriod[0]['id']);
                    if (count($inscription)>0){
                        if (self::validateWithdrawSubjects($request['withdrawSubjects'],$inscription[0]['enrolledSubjects'])){
                            self::changeStatusSubjects($inscription[0]['id'],$organizationId,$request['withdrawSubjects']);
                            return response()->json(['message'=>'Ok'],200);
                        }
                        return response()->json(['message'=>'Materias invalidas'],206);
                    }
                    return response()->json(['message'=>'No hay inscripcion actual para usted'],206);
                }
                return response()->json(['message'=>'No se puede realizar retiros la fecha ya ha pasado'],206);
            }
            return response()->json(['message'=>'No hay periodo escolar en curso'],206);
        }
        return $isValid;
    }

    public static function getEnrolledStudentsInSchoolPeriod($teacherId,$schoolPeriodSubjectTeacherId,Request $request)
    {
        $organizationId = $request->header('organization_key');
        $request['teacher_id']=$teacherId;
        $isValid=TeacherService::validateTeacher($request);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (count($currentSchoolPeriod)>0){
                if (SchoolPeriodSubjectTeacher::existSchoolPeriodSubjectTeacherById($schoolPeriodSubjectTeacherId)){
                    $schoolPeriodSubjectTeacher= SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherById($schoolPeriodSubjectTeacherId);
                    if ($schoolPeriodSubjectTeacher[0]['teacher_id']==$teacherId && $schoolPeriodSubjectTeacher[0]['school_period_id']==$currentSchoolPeriod[0]['id'] ){
                        $enrolledStudents=StudentSubject::studentSubjectBySchoolPeriodSubjectTeacherId($schoolPeriodSubjectTeacherId);
                        if (count($enrolledStudents)>0){
                            return $enrolledStudents;
                        }
                        return response()->json(['message'=>'No tienes inscripciones'],206);
                    }
                }
                return response()->json(['message'=>'Materia invalida'],206);
            }
            return response()->json(['message'=>'No hay periodo escolar en curso'],206);
        }
        return $isValid;
    }

    public static function validateLoadNotes(Request $request,$schoolPeriodId)
    {
        $teacherId=$request['teacher_id'];
        $schoolPeriodSubjectTeacherId=$request['school_period_subject_teacher_id'];
        $studentNotes=$request['student_notes'];
        if (!SchoolPeriodSubjectTeacher::existSchoolPeriodSubjectTeacherById($schoolPeriodSubjectTeacherId)){
            return false;
        }
        $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherById($schoolPeriodSubjectTeacherId);
        if ($schoolPeriodSubjectTeacher[0]['teacher_id']!=$teacherId ||$schoolPeriodSubjectTeacher[0]['school_period_id']!=$schoolPeriodId){
            return false;
        }
        $enrolledStudents=StudentSubject::studentSubjectBySchoolPeriodSubjectTeacherId($schoolPeriodSubjectTeacherId)->toArray();
        if (count($enrolledStudents)<=0){
            return false;
        }
        foreach ($studentNotes as $studentNote){
            $found = false;
            foreach ($enrolledStudents as $enrolledStudent){
                if($enrolledStudent['id']==$studentNote['student_subject_id'] && $enrolledStudent['status']!='RET'){
                    $found =true;
                }
            }
            if ($found == false){
                return false;
            }
        }
        return true;
    }

    public static function changeNotes($studentNotes){
        $schoolPeriodStudentForUpdate= [];
        foreach($studentNotes as $studentNote){
            $studentSubject= StudentSubject::getStudentSubjectById($studentNote['student_subject_id'])->toArray();
            $studentSubject[0]['qualification']=$studentNote['qualification'];
            $studentSubjectPrepare = self::prepareArrayOfSubject($studentSubject[0],$studentSubject[0]['school_period_student_id'],false);
            StudentSubject::updateStudentSubject($studentSubject[0]['id'],$studentSubjectPrepare);
            if (!in_array($studentSubject[0]['school_period_student_id'],$schoolPeriodStudentForUpdate)){
                $schoolPeriodStudentsForUpdate[]=$studentSubject[0]['school_period_student_id'];
            }
        }
        return $schoolPeriodStudentsForUpdate;
    }

    public static function loadNotes(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $isValid=TeacherService::validateTeacher($request);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (count($currentSchoolPeriod)>0){
                if (self::validateLoadNotes($request,$currentSchoolPeriod[0]['id']) && $currentSchoolPeriod[0]['load_notes']==true){
                    $schoolPeriodsStudentForUpdate=self::changeNotes($request['student_notes']);
                    foreach ($schoolPeriodsStudentForUpdate as $schoolPeriodStudentForUpdate){
                        self::updateStatus($schoolPeriodStudentForUpdate,$organizationId);
                    }
                    return response()->json(['message'=>'Ok'],200);
                }
                return response()->json(['message'=>'Datos invalidos'],206);
            }
            return response()->json(['message'=>'No hay periodo escolar en curso'],206);
        }
        return $isValid;
    }
}
