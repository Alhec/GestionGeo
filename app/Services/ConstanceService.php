<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 05/10/19
 * Time: 04:29 PM
 */

namespace App\Services;

use App\Administrator;
use App\Organization;
use App\SchoolPeriodStudent;
use App\SchoolPeriodSubjectTeacher;
use App\Student;
use App\SchoolPeriod;
use App\StudentSubject;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;

class ConstanceService
{
    public static function constanceOfStudy(Request $request, $studentId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId);
        if (count($student)>0){
            $data['user_data']=UserService::getUserById($request,$student[0]['user_id'],'S');
            $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
            $data['organization_data']=Organization::getOrganization($organizationId);
            return $data;
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }

    public static function academicLoad(Request $request, $studentId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
        if (count($currentSchoolPeriod)>0 && Student::existStudentById($studentId)){
            $schoolPeriodStudent=SchoolPeriodStudent::findSchoolPeriodStudent($studentId,$currentSchoolPeriod[0]['id']);
            if (count($schoolPeriodStudent)>0){
                return$schoolPeriodStudent;
            }
            return response()->json(['message'=>'No tiene carga academica'],206);
        }
        return response()->json(['message'=>'No hay periodo escolar en curso o no existe el estudiante'],206);
    }

    public static function studentHistorical(Request $request, $studentId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $student = Student::getStudentById($studentId);
        if (count($student)>0){
            if (User::existUserById($student[0]['user_id'],'S',$organizationId)){
                $studentSubject =SchoolPeriod::getEnrolledSubjectsByStudent($studentId);
                if (count($studentSubject)>0){
                    return $studentSubject;
                }
                return response()->json(['message'=>'Aun no tiene historial'],206);
            }
        }
        return response()->json(['message'=>'No existe el estudiante'],206);
    }

    public static function teacherHistorical(Request $request, $teacherId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['teacher_id']=$teacherId;
            $isValid = TeacherService::validateTeacher($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $teacher = Teacher::getTeacherById($teacherId);
        if (count($teacher)>0){
            if (User::existUserById($teacher[0]['user_id'],'T',$organizationId)){
                $schoolPeriodSubjectTeacher = SchoolPeriod::getSubjectsByTeacher($teacherId);
                if (count($schoolPeriodSubjectTeacher)>0){
                    return $schoolPeriodSubjectTeacher;
                }
                return response()->json(['message'=>'Aun no tiene historial'],206);
            }
        }
        return response()->json(['message'=>'No existe el profesor'],206);
    }

    public static function constanceOfWorkTeacher(Request $request, $teacherId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['teacher_id']=$teacherId;
            $isValid = TeacherService::validateTeacher($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $teacher = Teacher::getTeacherById($teacherId);
        if (count($teacher)>0){
            $data['user_data']=UserService::getUserById($request,$teacher[0]['user_id'],'T');
            $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
            $data['organization_data']=Organization::getOrganization($organizationId);
            return $data;
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }

    public static function constanceOfWorkAdministrator(Request $request, $administratorId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $data=[];
        $administrator = Administrator::getAdministratorById($administratorId);
        if (count($administrator)>0){
            $data['user_data']=UserService::getUserById($request,$administrator[0]['user_id'],'A');
            $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
            $data['organization_data']=Organization::getOrganization($organizationId);
            return $data;
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }
}
