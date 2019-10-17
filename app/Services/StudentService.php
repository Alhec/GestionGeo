<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 29/08/19
 * Time: 02:41 PM
 */

namespace App\Services;

use App\Student;
use App\User;
use Illuminate\Http\Request;

class StudentService
{
    public static function validate(Request $request)
    {
        $request->validate([
            'school_program_id'=>'required|numeric',
            'student_type'=>'required|max:3|ends_with:REG,EXT,AMP,PER,PDO,ACT',
            'home_university'=>'required|max:70',
            'current_postgraduate'=>'max:70',
            'type_income'=>'max:30',
            'is_ucv_teacher?'=>'required|boolean',
            'guide_teacher_id'=>'numeric',
            'credits_granted'=>'numeric'
        ]);
    }

    public static function addStudent(Request $request)
    {
        self::validate($request);
        $result = UserService::addUser($request,'S');
        if ($result=="identification_email"){
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else if ($result=="organization"){
            return response()->json(['message'=>'No existe organizacion asociada'],206);
        }else{
            Student::addStudent([
                'user_id'=>$result,
                'school_program_id'=>$request['school_program_id'],
                'student_type'=>$request['student_type'],
                'home_university'=>$request['home_university'],
                'current_postgraduate'=>$request['current_postgraduate'],
                'is_ucv_teacher?'=>$request['is_ucv_teacher?'],
                'is_available_final_work?'=>false,
                'repeat_approved_subject?'=>false,
                'repeat_reprobated_subject?'=>false,
                'credits_granted'=>$request['credits_granted']
            ]);
            return UserService::getUserById($request,$result,'S');
        }
    }

    public static function validateUpdate(Request $request)
    {
        $request->validate([
            'is_available_final_work?'=>'boolean',
            'repeat_approved_subject?'=>'boolean',
            'repeat_reprobated_subject?'=>'boolean'
        ]);
    }

    public static function updateStudent(Request $request,$id)
    {
        self::validate($request);
        self::validateUpdate($request);
        $result = UserService::updateUser($request,$id,'S');
        if ($result=="user"){
            return response()->json(['message'=>'Usuario no encontrado'],206);
        }else if ($result=="organization"){
            return response()->json(['message'=>'No existe organizacion asociada'],206);
        }else if ($result=="identification_email"){
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else {
            Student::updateStudent($id,[
                'user_id'=>$id,
                'school_program_id'=>$request['school_program_id'],
                'student_type'=>$request['student_type'],
                'home_university'=>$request['home_university'],
                'current_postgraduate'=>$request['current_postgraduate'],
                'is_ucv_teacher?'=>$request['is_ucv_teacher?'],
                'is_available_final_work?'=>$request['is_available_final_work?'],
                'repeat_approved_subject?'=>$request['repeat_approved_subject?'],
                'repeat_reprobated_subject?'=>$request['repeat_reprobated_subject?'],
                'credits_granted'=>$request['credits_granted']
            ]);
            return UserService::getUserById($request,$id,'S');
        }
    }

    public static function validateStudent(Request $request)
    {
        $organizationId = $request->header('organization_key');
        if (Student::existStudentById($request['student_id'])){
            $student = Student::getStudentById($request['student_id']);
            if (!User::existUserById($student[0]['user_id'],'S',$organizationId)) {
                return response()->json(['message'=>'Usuario no encontrado'],206);
            }
            if ($student[0]['user_id'] != auth()->user()['id']  ){
                return response()->json(['message'=>'Unauthorized'],401);
            }
        }else{
            return response()->json(['message'=>'Usuario no encontrado'],206);
        }
        return 'valid';
    }
}
