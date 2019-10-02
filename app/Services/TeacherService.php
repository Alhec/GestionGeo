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
        if ($result=="identification_email"){
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else if ($result=="organization"){
            return response()->json(['message'=>'No existe organizacion asociada'],206);
        }else{
            Teacher::addTeacher([
                'user_id'=>$result,
                'teacher_type'=>$request['teacher_type'],
            ]);
            return UserService::getUserById($request,$result,'T');
        }
    }

    public static function updateTeacher(Request $request, $id)
    {
        self::validate($request);
        $result =UserService::updateUser($request,$id,'T');
        if ($result=="user"){
            return response()->json(['message'=>'Usuario no encontrado'],206);
        }else if ($result=="organization"){
            return response()->json(['message'=>'No existe organizacion asociada'],206);
        }else if ($result=="identification_email"){
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else {
            Teacher::updateTeacher($id, [
                'user_id' => $id,
                'teacher_type' => $request['teacher_type']
            ]);
            return UserService::getUserById($request, $id, 'T');
        }
    }

    public static function validateTeacher(Request $request)
    {
        $organizationId = $request->header('organization_key');
        if (Teacher::existTeacherById($request['teacher_id'])){
            $teacher = Teacher::getTeacherById($request['teacher_id']);
            if (!User::existUserById($teacher[0]['user_id'],'S',$organizationId)) {
                return response()->json(['message'=>'Usuario no encontrado'],206);
            }
            if ($teacher[0]['user_id'] != auth()->user()['id']  ){
                return response()->json(['message'=>'Unauthorized'],401);
            }
        }else{
            return response()->json(['message'=>'Usuario no encontrado'],206);
        }
        return 'valid';
    }
}
