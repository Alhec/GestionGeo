<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 28/08/19
 * Time: 03:59 PM
 */

namespace App\Services;


use Illuminate\Http\Request;
use App\User;
use App\Teacher;

class TeacherService
{
    public static function validate(Request $request)
    {
        $request->validate([
            'teacher_type'=>'max:3|ends_with:INS,ASI,AGR,ASO,TIT',
        ]);
    }

    public static function addTeacher(Request $request)
    {
        self::validate($request);
        $result =UserService::addUser($request,'T');
        if (is_int($result)){
            if ($result==1){
                return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
            }
            if ($result==2){
                return response()->json(['message'=>'No existe organizacion asociada'],206);
            }
        }
        $teacher = User::findUser($request['identification'],'T');
        Teacher::addTeacher([
            'user_id'=>$teacher[0]['id'],
            'teacher_type'=>$request['teacher_type'],
        ]);
        return UserService::getUserById($request,$teacher[0]['id'],'T');
    }

    public static function updateTeacher(Request $request, $id)
    {
        self::validate($request);
        $result =UserService::updateUser($request,$id,'T');
        if (is_int($result)){
            if ($result==3){
                return response()->json(['message'=>'Usuario no encontrado'],206);
            }
        }
        Teacher::updateTeacher($id,[
            'user_id'=>$id,
            'teacher_type'=>$request['teacher_type']
        ]);
        return UserService::getUserById($request,$id,'T');
    }
}