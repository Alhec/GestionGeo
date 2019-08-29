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
            'postgraduate_id'=>'required',
            'student_type'=>'required|max:3|ends_with:REG,EXT,AMP',
            'home_university'=>'required|max:70',
            'current_postgraduate'=>'max:70',
        ]);
    }

    public static function addStudent(Request $request)
    {
        self::validate($request);
        $result = UserService::addUser($request,'S');
        if (is_int($result)){
            if ($result==1){
                return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
            }
            if ($result==2){
                return response()->json(['message'=>'No existe organizacion asociada'],206);
            }
        }
        $student = User::findUser($request['identification'],'S');
        Student::addStudent([
            'user_id'=>$student[0]['id'],
            'postgraduate_id'=>$request['postgraduate_id'],
            'student_type'=>$request['student_type'],
            'home_university'=>$request['home_university'],
            'current_postgraduate'=>$request['current_postgraduate'],
            'degrees'=>$request['degrees'],
        ]);
        return UserService::getUserById($request,$student[0]['id'],'S');
    }

    public static function updateStudent(Request $request,$id)
    {
        self::validate($request);
        $result = UserService::updateUser($request,$id,'S');
        if (is_int($result)){
            if ($result==3){
                return response()->json(['message'=>'Usuario no encontrado'],206);
            }
        }
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