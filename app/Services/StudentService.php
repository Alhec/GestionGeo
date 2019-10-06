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
            'postgraduate_id'=>'required|numeric',
            'student_type'=>'required|max:3|ends_with:REG,EXT,AMP',
            'home_university'=>'required|max:70',
            'current_postgraduate'=>'max:70',
            'type_income'=>'max:30'
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
                'postgraduate_id'=>$request['postgraduate_id'],
                'student_type'=>$request['student_type'],
                'home_university'=>$request['home_university'],
                'current_postgraduate'=>$request['current_postgraduate'],
                'degrees'=>$request['degrees'],
            ]);
            return UserService::getUserById($request,$result,'S');
        }
    }

    public static function updateStudent(Request $request,$id)
    {
        self::validate($request);
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
                'postgraduate_id'=>$request['postgraduate_id'],
                'student_type'=>$request['student_type'],
                'home_university'=>$request['home_university'],
                'current_postgraduate'=>$request['current_postgraduate'],
                'degrees'=>$request['degrees'],
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
