<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 29/08/19
 * Time: 02:41 PM
 */

namespace App\Services;

use App\Degree;
use App\Equivalence;
use App\FinalWork;
use App\Log;
use App\SchoolProgram;
use App\Student;
use App\StudentSubject;
use App\Subject;
use App\User;
use Illuminate\Http\Request;

class StudentService
{
    const taskError = 'No se puede proceder con la tarea';
    const busyCredential = 'Identificacion o Correo ya registrados';
    const notFoundUser = 'Usuario no encontrado';
    const invalidTeacher = 'Profesor guia invalido';
    const invalidSchoolProgram = 'Programa escolar invalido';
    const taskPartialError = 'No se pudo proceder con la tarea en su totalidad';
    const notSendEmail = 'No se pudo enviar el correo electronico';
    const studentInProgram = 'El estudiante no ha culminado su programa escolar actual';
    const noAction = "No esta permitido realizar esa accion";
    const studentProgram = "El estudiante esta cursando un programa academico";
    const studentHasProgram = "El estudiante ya curso el programa escolar";
    const invalidEquivalences = "Equivalencias invalidas";
    const unauthorized = "Unauthorized";
    const notWarningStudent="Todos los estudiantes estan en un estatus regular";
    const ok = "OK";


    const logCreateStudent = 'Creo la entidad student para ';
    const logUpdateStudent = 'Actualizo la entidad student para ';
    const logDeleteStudent = 'Elimino la entidad student para ';

    public static function validate(Request $request)
    {
        $request->validate([
            'guide_teacher_id'=>'numeric|nullable',
            'student_type'=>'required|max:3|ends_with:REG,EXT,AMP,PER,PDO,ACT',
            'home_university'=>'required|max:100',
            'current_postgraduate'=>'max:100',
            'type_income'=>'max:30',
            'is_ucv_teacher'=>'boolean',
            'credits_granted'=>'numeric|required',
            'with_work'=>'boolean',
            'degrees.*.degree_obtained'=>'required|max:3|ends_with:TSU,TCM,Dr,Esp,Ing,MSc,Lic',
            'degrees.*.degree_name'=>'required|max:50',
            'degrees.*.degree_description'=>'max:200',
            'degrees.*.university'=>'required|max:100',
            'equivalences.*.subject_id'=>'required|numeric',
            'equivalences.*.qualification'=>'required|numeric',
        ]);
    }

    public static function validateSchoolProgramId(Request $request){
        $request->validate([
            'school_program_id'=>'required|numeric',
        ]);
    }

    public static function addEquivalencesAndDegrees(Request $request,$studentId)
    {
        if (isset($request['degrees'])){
            foreach ($request['degrees'] as $degree){
                $degree['student_id']=$studentId;
                $result = Degree::addDegree($degree);
                if (is_numeric($result)&& $result == 0){
                    return 0;
                }
            }
        }
        if (isset($request['equivalences'])){
            foreach($request['equivalences'] as $equivalence){
                $result = Equivalence::addEquivalence([
                    'student_id'=>$studentId,
                    'subject_id'=>$equivalence['subject_id'],
                    'qualification'=>$equivalence['qualification']
                ]);
                if (is_numeric($result) && $result == 0){
                    return 0;
                }
            }
        }
    }

    public static function addStudent($userId,Request $request)
    {
        return Student::addStudent([
            'school_program_id'=>$request['school_program_id'],
            'user_id'=>$userId,
            'guide_teacher_id'=>$request['guide_teacher_id'],
            'student_type'=>$request['student_type'],
            'home_university'=>$request['home_university'],
            'current_postgraduate'=>$request['current_postgraduate'],
            'type_income'=>$request['type_income'],
            'is_ucv_teacher'=>$request['is_ucv_teacher'],
            'is_available_final_work'=>false,
            'credits_granted'=>$request['credits_granted'],
            'with_work'=>$request['with_work'],
            'end_program'=>false,
            'test_period'=>false,
            'current_status'=>'REG'
        ]);
    }

    public static function validateEquivalences($organizationId,$subjects,$schoolProgramId)
    {
        $subjectsInBd=Subject::getSubjectsBySchoolProgram($schoolProgramId,$organizationId);
        if (is_numeric($subjects) && $subjects==0){
            return 0;
        }
        if (count($subjectsInBd)<1){
            return false;
        }
        $subjectsId=array_column($subjectsInBd->toArray(),'id');
        foreach ($subjects as $subject){
            if (!in_array($subject['subject_id'],$subjectsId)){
                return false;
            }
            if ($subject['qualification']>20 ||$subject['qualification']<0){
                return false;
            }
        }
        return true;
    }

    public static function addNewStudent(Request $request,$organizationId)
    {
        self::validateSchoolProgramId($request);
        self::validate($request);
        if (isset($request['guide_teacher_id'])){
            $existTeacher=User::existUserById($request['guide_teacher_id'],'T',$organizationId);
        }else{
            $existTeacher=true;
        }
        $existSchoolProgram = SchoolProgram::existSchoolProgramById($request['school_program_id'],$organizationId);
        if ((is_numeric($existTeacher)&&$existTeacher==0)||(is_numeric($existSchoolProgram)&&$existSchoolProgram==0)){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!$existTeacher){
            return response()->json(['message'=>self::invalidTeacher],206);
        }
        if (!$existSchoolProgram){
            return response()->json(['message'=>self::invalidSchoolProgram],206);
        }
        if (isset($request['equivalences'])){
            $validEquivalence =self::validateEquivalences($organizationId,$request['equivalences'],$request['school_program_id']);
            if (!$validEquivalence){
                return response()->json(['message'=>self::invalidEquivalences],206);
            }
        }
        $userId = UserService::addUser($request,'S',$organizationId);
        if (is_string($userId)&&$userId =='busy_credential'){
            return response()->json(['message'=>self::busyCredential],206);
        }else if (is_numeric($userId)&&$userId==0){
            return response()->json(['message'=>self::taskError],206);
        }else{
            $studentId=self::addStudent($userId,$request);
            if (is_numeric($studentId)&&$studentId==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $result = self::addEquivalencesAndDegrees($request,$studentId);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logCreateStudent.$request['first_name'].
                ' '.$request['first_surname']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskPartialError],401);
            }
            $result = EmailService::userCreate($userId,$organizationId,'S');
            if ($result==0){
                return response()->json(['message'=>self::notSendEmail],206);
            }
            return UserService::getUserById($userId,'S',$organizationId);
        }
    }

    public static function addStudentContinue(Request $request, $userId, $organizationId)
    {
        self::validateSchoolProgramId($request);
        self::validate($request);
        $result = Student::studentHasProgram($userId);
        if (is_numeric($result)&&$result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($result){
            return response()->json(['message'=>self::studentInProgram],206);
        }
        if (isset($request['guide_teacher_id'])){
            $existTeacher=User::existUserById($request['guide_teacher_id'],'T',$organizationId);
        }else{
            $existTeacher=true;
        }
        $existSchoolProgram = SchoolProgram::existSchoolProgramById($request['school_program_id'],$organizationId);
        if ((is_numeric($existTeacher)&&$existTeacher==0)||(is_numeric($existSchoolProgram)&&$existSchoolProgram==0)){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!$existTeacher){
            return response()->json(['message'=>self::invalidTeacher],206);
        }
        if (!$existSchoolProgram){
            return response()->json(['message'=>self::invalidSchoolProgram],206);
        }
        $result=Student::existStudentInProgram($userId,$request['school_program_id']);
        if (is_numeric($result)&&$result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($result){
            return response()->json(['message'=>self::studentHasProgram],206);
        }
        $result = UserService::updateUser($request,$userId,'S',$organizationId);
        if ($result==="not_found"){
            return response()->json(['message'=>self::notFoundUser],206);
        }else if (is_numeric($result)&&$result==0){
            return response()->json(['message'=>self::taskError],206);
        }else if ($result==="busy_credential"){
            return response()->json(['message'=>self::busyCredential],206);
        }else {
            $studentId=self::addStudent($userId,$request);
            if (is_numeric($studentId)&&$studentId==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $result = self::addEquivalencesAndDegrees($request,$studentId);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logCreateStudent.$request['first_name'].
                ' '.$request['first_surname']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskPartialError],401);
            }
            return UserService::getUserById($userId,'S',$organizationId);
        }
    }

    public static function validateUpdate(Request $request)
    {
        $request->validate([
            'student_id'=>'numeric|required',
            'is_available_final_work'=>'boolean|required',
            'test_period'=>'boolean|required',
            'current_status'=>'max:5|required|ends_with:RET-A,RET-B,DES-A,DES-B,RIN-A,RIN-B,REI-A,REI-B,REG,ENDED',//REI REINCORPORADO RIN REINGRESO
        ]);
    }

    public static function updateStudent(Request $request, $userId, $organizationId)
    {
        self::validate($request);
        self::validateUpdate($request);
        if (isset($request['guide_teacher_id'])){
            $existTeacher=User::existUserById($request['guide_teacher_id'],'T',$organizationId);
        }else{
            $existTeacher=true;
        }
        if ((is_numeric($existTeacher)&&$existTeacher==0)){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!$existTeacher){
            return response()->json(['message'=>self::invalidTeacher],206);
        }
        $student = Student::getStudentById($request['student_id'],$organizationId);
        if (is_numeric($student)&&$student==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($student)<1){
            return response()->json(['message'=>self::notFoundUser],206);
        }
        if (isset($request['equivalences'])){
            $validEquivalence =self::validateEquivalences($organizationId,$request['equivalences'],$student[0]['school_program_id']);
            if (!$validEquivalence){
                return response()->json(['message'=>self::invalidEquivalences],206);
            }
        }
        $result = UserService::updateUser($request,$userId,'S',$organizationId);
        if ($result==="not_found"){
            return response()->json(['message'=>self::notFoundUser],206);
        }else if (is_numeric($result)&&$result==0){
            return response()->json(['message'=>self::taskError],206);
        }else if ($result==="busy_credential"){
            return response()->json(['message'=>self::busyCredential],206);
        }else {
            if ($request['current_status']=='RET-B'||$request['current_status']=='ENDED'){
                $request['end_program']=true;
            }else{
                $request['end_program']=false;
            }
            $result = Student::updateStudent($request['student_id'],[
                'school_program_id'=>$student[0]['school_program_id'],
                'user_id'=>$userId,
                'guide_teacher_id'=>$request['guide_teacher_id'],
                'student_type'=>$request['student_type'],
                'home_university'=>$request['home_university'],
                'current_postgraduate'=>$request['current_postgraduate'],
                'type_income'=>$request['type_income'],
                'is_ucv_teacher'=>$request['is_ucv_teacher'],
                'is_available_final_work'=>$request['is_available_final_work'],
                'credits_granted'=>$request['credits_granted'],
                'with_work'=>$request['with_work'],
                'end_program'=>$request['end_program'],
                'test_period'=>$request['test_period'],
                'current_status'=>$request['current_status'],
            ]);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $deleteDegrees = Degree::deleteDegree($request['student_id']);
            $deleteEquivalences = Equivalence::deleteEquivalence($request['student_id']);
            if ((is_numeric($deleteDegrees)&&$deleteDegrees==0)||(is_numeric($deleteEquivalences)&&$deleteEquivalences==0)){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $result=self::addEquivalencesAndDegrees($request,$request['student_id']);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logUpdateStudent.$request['first_name'].
                ' '.$request['first_surname']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskPartialError],401);
            }
            return UserService::getUserById($userId,'S',$organizationId);
        }
    }

    public static function deleteStudent($userId,$studentId,$organizationId)
    {
        $user = User::getUserById($userId,'S',$organizationId);
        if (is_numeric($user)&& $user == 0 ){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($user)<1){
            return response()->json(['message'=>self::notFoundUser],206);
        }
        if (count($user[0]['student'])>=2){
            $result = Student::deleteStudent($userId,$studentId);
            if (is_numeric($result)&&$result == 0 ){
                return response()->json(['message'=>self::taskError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logDeleteStudent.$user[0]['first_name'].
                ' '.$user[0]['first_surname']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskError],401);
            }
            return UserService::getUserById($userId,'S',$organizationId);
        }
        return response()->json(['message'=>self::noAction],206);
    }

    public static function validateStudent($organizationId,$studentId)
    {
        $existStudentById = Student::existStudentById($studentId,$organizationId);
        if (is_numeric($existStudentById) && $existStudentById == 0) {
            return response()->json(['message' => self::taskError], 206);
        }
        if ($existStudentById) {
            $student = Student::getStudentById($studentId, $organizationId);
            if (is_numeric($student) && $student == 0) {
                return response()->json(['message' => self::taskError], 206);
            }
            if (count($student) > 0) {
                if (auth()->payload()['user']->user_type=='A'){
                    return 'valid';
                }
                if (auth()->payload()['user']->user_type=='S'){
                    $studentsId = array_column(auth()->payload()['user']->student, 'id');
                    if (in_array($studentId, $studentsId)) {
                        return 'valid';
                    }
                }
                return response()->json(['message' => self::unauthorized], 206);
            }
            return response()->json(['message' => self::notFoundUser], 401);
        }
    }

    public static function warningStudent($organizationId)
    {
        $warningStudent=Student::warningStudent($organizationId);
        if (is_numeric($warningStudent)&&$warningStudent==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($warningStudent)>0){
            return $warningStudent;
        }
        return response()->json(['message' => self::notWarningStudent], 200);
    }

    public static function warningOrAvailableWorkToStudent($organizationId)
    {
        $students=Student::getStudentActive($organizationId);
        if (is_numeric($students)&&$students==0){
            return 0;
        }
        if (count($students)>0){
            foreach ($students as $student){
                $update =false;
                $totalQualification = InscriptionService::getTotalQualification($student['id']);
                if (is_string($totalQualification)&&$totalQualification=='e'){
                    return 0;
                }
                $cantSubjectsEnrolled=StudentSubject::cantAllSubjectsEnrolledWithoutRETCUR($student['id']);
                if (is_string($cantSubjectsEnrolled)&&$cantSubjectsEnrolled=='e'){
                    return 0;
                }
                if($cantSubjectsEnrolled>0 && ($totalQualification/$cantSubjectsEnrolled)<14){
                    $student['testPeriod']=true;
                    $update = true;
                }
                $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
                if (is_numeric($schoolProgram) && $schoolProgram===0){
                    return 0;
                }
                if (count($schoolProgram)>0){
                    if ($schoolProgram[0]['conducive_to_degree']){
                        $project = FinalWork::getFinalWorksByStudent($student['id'], true);
                        if (is_numeric($project)&&$project===0){
                            return 0;
                        }
                        $notApprovedProject = FinalWork::existNotApprovedFinalWork($student['id'], true);
                        if(is_numeric($notApprovedProject)&&$notApprovedProject===0){
                            return 0;
                        }
                        if (!$notApprovedProject && count($project)>0){
                            $student['is_available_final_work']=true;
                            $update = true;
                        }
                    }
                }
                if ($update){
                    $student = Student::updateStudent($student['id'],$student->toArray());
                    if (is_numeric($student)&& $student==0){
                        return 0;
                    }
                }
            }
        }
        return 'emptyStudent';
    }
}
