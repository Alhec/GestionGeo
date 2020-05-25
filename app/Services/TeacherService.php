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
    const taskError = 'No se puede proceder con la tarea';
    const busyCredential = 'Identificacion o Correo ya registrados';
    const notFoundUser = 'Usuario no encontrado';
    const taskPartialError = 'No se pudo proceder con la tarea en su totalidad';
    const notSendEmail = 'No se pudo enviar el correo electronico';
    const noAction = "No esta permitido realizar esta accion";
    const unauthorized = "Unauthorized";

    public static function validate(Request $request)
    {
        $request->validate([
            'teacher_type'=>'required|max:3|ends_with:INS,ASI,AGR,ASO,TIT,JUB,INV',
            'dedication'=>'required|max:3|ends_with:MT,CON,TC,EXC',
            'home_institute'=>'max:100',
            'country'=>'max:20'
        ]);
    }

    public static function addTeacher(Request $request,$organizationId)
    {
        self::validate($request);
        $user =UserService::addUser($request,'T',$organizationId);
        if ($user=="busy_credential"){
            return response()->json(['message'=>self::busyCredential],206);
        }else if (is_numeric($user)&&$user==0){
            return response()->json(['message'=>self::taskError],206);
        }else{
            $result = Teacher::addTeacher([
                'id'=>$user,
                'teacher_type'=>$request['teacher_type'],
                'dedication'=>$request['dedication'],
                'home_institute'=>$request['home_institute'],
                'country'=>$request['country'],
            ]);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $result = EmailService::userCreate($user,$organizationId,'T');
            if ($result==0){
                return response()->json(['message'=>self::notSendEmail],206);
            }
            return UserService::getUserById($user,'T',$organizationId);
        }
    }

    public static function updateTeacher(Request $request, $id,$organizationId)
    {
        self::validate($request);
        $result =UserService::updateUser($request,$id,'T',$organizationId);
        if ($result=="not_found"){
            return response()->json(['message'=>self::notFoundUser],206);
        }else if (is_numeric($result)&&$result==0){
            return response()->json(['message'=>self::taskError],206);
        }else if ($result=="busy_credential"){
            return response()->json(['message'=>self::busyCredential],206);
        }else {
            $result = Teacher::updateTeacher($id, [
                'id' => $id,
                'teacher_type' => $request['teacher_type'],
                'dedication'=>$request['dedication'],
                'home_institute'=>$request['home_institute'],
                'country'=>$request['country'],
            ]);
            if (is_numeric($result)&&$result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            return UserService::getUserById($id, 'T',$organizationId);
        }
    }

    public static function validateTeacher($teacherId,$organizationId)
    {
        $existTeacherId=User::existUserById($teacherId,'T',$organizationId);
        if (is_numeric($existTeacherId)&&$existTeacherId==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($existTeacherId){
            if(auth()->payload()['user']->user_type!='A' && auth()->payload()['user']->id!=$teacherId){
                return response()->json(['message'=>self::unauthorized],206);
            }
            return 'valid';
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }
}
