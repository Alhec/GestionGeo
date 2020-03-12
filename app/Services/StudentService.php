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
    const studentHasProgram = "El estudiante ya esta en el programa";
    const invalidEquivalences = "Equivalencias invalidas";
    const unauthorized = "Unauthorized";
    const notWarningStudent="Todos los estudiantes estan en un estatus regular";
    const ok = "OK";

    public static function validate(Request $request)
    {
        $request->validate([
            'school_program_id'=>'required|numeric',
            'student_type'=>'required|max:3|ends_with:REG,EXT,AMP,PER,PDO,ACT',
            'home_university'=>'required|max:70',
            'current_postgraduate'=>'max:70',
            'type_income'=>'max:30',
            'is_ucv_teacher'=>'boolean',
            'guide_teacher_id'=>'numeric',
            'credits_granted'=>'numeric',
            'with_work'=>'boolean',
            'degrees.*.degree_obtained'=>'required|max:3|ends_with:TSU,TCM,Dr,Esp,Ing,MSc,Lic',
            'degrees.*.degree_name'=>'required|max:50',
            'degrees.*.degree_description'=>'max:200',
            'degrees.*.university'=>'required|max:50',
            'equivalences.*.subject_id'=>'required|numeric',
            'equivalences.*.qualification'=>'required|numeric',
        ]);
    }

    public static function addEquivalencesAndDegrees($request,$studentId)
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

    public static function addStudent($userId,$request)
    {
        return Student::addStudent([
            'user_id'=>$userId,
            'school_program_id'=>$request['school_program_id'],
            'student_type'=>$request['student_type'],
            'home_university'=>$request['home_university'],
            'current_postgraduate'=>$request['current_postgraduate'],
            'is_ucv_teacher'=>$request['is_ucv_teacher'],
            'is_available_final_work'=>false,
            'repeat_approved_subject'=>false,
            'repeat_reprobated_subject'=>false,
            'credits_granted'=>$request['credits_granted'],
            'guide_teacher_id'=>$request['guide_teacher_id'],
            'with_work'=>$request['with_work'],
            'end_program'=>false,
            'type_income'=>$request['type_income']
        ]);
    }

    public static function validateEquivalences($organizationId,$subjects,$schoolProgramId)
    {
        $subjectsInBd=Subject::getSubjectsBySchoolProgram($schoolProgramId,$organizationId);
        if (is_numeric($subjects) && $subjects==0){
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
        if (!self::validateEquivalences($organizationId,$request['equivalences'],$request['school_program_id'])){
            return response()->json(['message'=>self::invalidEquivalences],206);
        }
        $userId = UserService::addUser($request,'S',$organizationId);
        if ($userId=='busy_credential'){
            return response()->json(['message'=>self::busyCredential],206);
        }else if (is_numeric($userId)&&$userId==0){
            return response()->json(['message'=>self::taskError],206);
        }else{
            $studentId=self::addStudent($userId,$request);
            if (is_numeric($studentId)&&$studentId==0){
                return response()->json(['message'=>self::taskError],206);
            }
            $result = self::addEquivalencesAndDegrees($request,$studentId);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $result = EmailService::userCreate($userId,$organizationId,'S');
            if ($result==0){
                return response()->json(['message'=>self::notSendEmail],206);
            }
            return UserService::getUserById($request,$userId,'S',$organizationId);
        }
    }

    public static function validateUpdate(Request $request)
    {
        $request->validate([
            'student_type'=>'required|max:3|ends_with:REG,EXT,AMP,PER,PDO,ACT',
            'home_university'=>'required|max:70',
            'current_postgraduate'=>'max:70',
            'type_income'=>'max:30',
            'is_ucv_teacher'=>'boolean',
            'guide_teacher_id'=>'numeric',
            'credits_granted'=>'numeric',
            'with_work'=>'boolean',
            'degrees.*.degree_obtained'=>'required|max:3|ends_with:TSU,TCM,Dr,Esp,Ing,MSc,Lic',
            'degrees.*.degree_name'=>'required|max:50',
            'degrees.*.degree_description'=>'max:200',
            'degrees.*.university'=>'required|max:50',
            'equivalences.*.subject_id'=>'required|numeric',
            'equivalences.*.qualification'=>'required|numeric',
            'student_id'=>'numeric|required',
            'is_available_final_work'=>'boolean|required',
            'repeat_approved_subject'=>'boolean|required',
            'repeat_reprobated_subject'=>'boolean|required',
            'end_program'=>'boolean|required',
            'test_period'=>'boolean',
            'active'=>'boolean|required'
        ]);
    }

    public static function addStudentContinue($request,$id,$organizationId)
    {
        self::validate($request);
        $result = Student::studentHasProgram($id);
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
        $result=Student::existStudentInProgram($id,$request['school_program_id']);
        if (is_numeric($result)&&$result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($result){
            return response()->json(['message'=>self::studentHasProgram],206);
        }
        $result = UserService::updateUser($request,$id,'S',$organizationId);
        if ($result=="not_found"){
            return response()->json(['message'=>self::notFoundUser],206);
        }else if (is_numeric($result)&&$result==0){
            return response()->json(['message'=>self::taskError],206);
        }else if ($result=="busy_credential"){
            return response()->json(['message'=>self::busyCredential],206);
        }else {
            $studentId=self::addStudent($id,$request);
            if (is_numeric($studentId)&&$studentId==0){
                return response()->json(['message'=>self::taskError],206);
            }
            $result = self::addEquivalencesAndDegrees($request,$studentId);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            return UserService::getUserById($request,$id,'S',$organizationId);
        }
    }

    public static function updateStudent(Request $request,$id,$organizationId)
    {
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
        $result = UserService::updateUser($request,$id,'S',$organizationId);
        if ($result=="not_found"){
            return response()->json(['message'=>self::notFoundUser],206);
        }else if (is_numeric($result)&&$result==0){
            return response()->json(['message'=>self::taskError],206);
        }else if ($result=="busy_credential"){
            return response()->json(['message'=>self::busyCredential],206);
        }else {
            $student = Student::activeStudentId($id);
            if (is_numeric($student)&&$student==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (!$request['end_program'] && count($student)>0 && $student[0]['id']!=$request['student_id']) {
                return response()->json(['message'=>self::noAction],206);
            }
            $student = Student::getStudentById($request['student_id'],$organizationId);
            if (is_numeric($student)&&$student==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if ($request['current_status']=='RET-B'){
                $request['end_program']=true;
            }else{
                $request['end_program']=false;
            }
            $result = Student::updateStudent($request['student_id'],[
                'user_id'=>$id,
                'school_program_id'=>$student[0]['school_program_id'],
                'student_type'=>$request['student_type'],
                'home_university'=>$request['home_university'],
                'current_postgraduate'=>$request['current_postgraduate'],
                'is_ucv_teacher'=>$request['is_ucv_teacher'],
                'is_available_final_work'=>$request['is_available_final_work'],
                'repeat_approved_subject'=>$request['repeat_approved_subject'],
                'repeat_reprobated_subject'=>$request['repeat_reprobated_subject'],
                'credits_granted'=>$request['credits_granted'],
                'guide_teacher_id'=>$request['guide_teacher_id'],
                'with_work'=>$request['with_work'],
                'end_program'=>$request['end_program'],
                'test_period'=>$request['test_period'],
                'current_status'=>$request['current_status'],
                'type_income'=>$request['type_income']
            ]);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskError],206);
            }
            $deleteDegrees = Degree::deleteDegree($request['student_id']);
            $deleteEquivalences = Equivalence::deleteEquivalence($request['student_id']);
            if ((is_numeric($deleteDegrees)&&$deleteDegrees==0)||(is_numeric($deleteEquivalences)&&$deleteEquivalences==0)){
                return response()->json(['message'=>self::taskError],206);
            }
            $result=self::addEquivalencesAndDegrees($request,$request['student_id']);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskError],206);
            }
            return UserService::getUserById($request,$id,'S',$organizationId);
        }
    }

    public static function deleteStudent($userId,$studentId,$request,$organizationId)
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
            return UserService::getUserById($request,$userId,'S',$organizationId);
        }
        return response()->json(['message'=>self::noAction],206);
    }

    public static function deleteStudentUser($userId,$request,$organizationId)
    {
        $user = User::getUserById($userId,'S',$organizationId);
        if (is_numeric($user)&& $user == 0 ){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($user)<1){
            return response()->json(['message'=>self::notFoundUser],206);
        }
        if (count($user[0]['student'])>=2){
            $result = User::deleteUser($userId);
            if (is_numeric($result)&&$result == 0 ){
                return response()->json(['message'=>self::taskError],206);
            }
            return response()->json(['message'=>self::ok],206);
        }
        return response()->json(['message'=>self::noAction],206);
    }

    public static function validateStudent(Request $request, $organizationId,$studentId)
    {
        $existStudentById = Student::existStudentById($studentId,$organizationId);
        if (is_numeric($existStudentById) && $existStudentById == 0) {
            return response()->json(['message' => self::taskError], 206);
        }
        if ($existStudentById) {
            $student = Student::getStudentById($request['student_id'], $organizationId);
            if (is_numeric($student) && $student == 0) {
                return response()->json(['message' => self::taskError], 206);
            }
            if (count($student) > 0) {
                $studentsId = array_column(auth()->payload()['user']->student, 'id');
                if (in_array($studentId, $studentsId)) {
                    return 'valid';
                }
                return response()->json(['message' => self::unauthorized], 206);
            }
            return response()->json(['message' => self::notFoundUser], 401);
        }
    }

    public static function warningStudent(Request $request,$organizationId)
    {
        $warningStudent=Student::warningStudent($organizationId);
        if (is_numeric($warningStudent)&&$warningStudent==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($warningStudent)>0){
            return $warningStudent;
        }
        return response()->json(['message' => self::notWarningStudent], 206);
    }

    public static function warningUpdateStudent($organizationId)
    {
        $students=Student::getStudentActive($organizationId);
        if (is_numeric($students)&&$students==0){
            return 0;
        }
        if (count($students)>0){
            foreach ($students as $student){
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
