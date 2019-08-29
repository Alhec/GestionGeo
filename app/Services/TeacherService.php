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
        UserServices::addUser($request,'T');
        $teacher = User::findUser($request['identification'],'T');
        Teacher::addTeacher([
            'user_id'=>$teacher[0]['id'],
            'teacher_type'=>$request['teacher_type'],
        ]);
        return UserServices::getUserById($request,$teacher[0]['id'],'T');
    }

    public static function updateTeacher(Request $request, $id)
    {
        self::validate($request);
        UserServices::updateUser($request,$id,'T');
        Teacher::updateTeacher($id,[
            'user_id'=>$id,
            'teacher_type'=>$request['teacher_type']
        ]);
        return UserServices::getUserById($request,$id,'T');
    }
}