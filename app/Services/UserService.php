<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 27/08/19
 * Time: 02:24 PM
 */

namespace App\Services;


use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;


class UserService
{
    const taskError = 'No se puede proceder con la tarea';
    const emptyUser = 'No existen usuarios con ese perfil';
    const notFoundUser = 'Usuario no encontrado';
    const ok = 'OK';
    const notFoundActiveUser = 'No existen usuarios activos con ese perfil';
    const invalidPassword = 'La clave no puede ser igual a la anterior';
    const invalidNewPassword = 'La clave actual esta mal';
    const busyCredential = 'Identificacion o Correo ya registrados';

    public static function getUsers(Request $request, $userType,$organizationId)
    {
        $users= User::getUsers($userType,$organizationId);
        if (is_numeric($users) && $users == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($users)>0){
            return $users;
        }
        return response()->json(['message'=>self::emptyUser],206);
    }

    public static function getUserById(Request $request, $userId, $userType,$organizationId)
    {
        $user = User::getUserById($userId,$userType,$organizationId);
        if (is_numeric($user) && $user == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($user)>0){
            return $user[0];
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'identification'=>'required|max:20',
            'first_name'=>'required|max:20',
            'second_name'=>'max:20',
            'first_surname'=>'required|max:20',
            'second_surname'=>'max:20',
            'telephone'=>'max:15',
            'mobile'=>'required|max:15',
            'work_phone'=>'max:15',
            'email'=>'required|max:30|email',
            'level_instruction'=>'required|max:3|ends_with:TSU,TCM,Dr,Esp,Ing,MSc,Lic',
            'with_disabilities'=>'boolean',
            'sex'=>'required|max:1|ends_with:M,F',
            'nationality'=>'required|max:1|ends_with:V,E',
        ]);
    }

    public static function addUser(Request $request,$userType,$organizationId)
    {
        self::validate($request);
        $existUserByIdentification=User::existUserByIdentification($request['identification'],$userType,$organizationId);
        $existUserByEmail=User::existUserByEmail($request['email'],$userType,$organizationId);
        if ((is_numeric($existUserByIdentification)&&$existUserByIdentification==0)||(is_numeric($existUserByEmail)&&$existUserByEmail==0)){
            return 0;
        }
        if (!($existUserByIdentification)AND!($existUserByEmail)){
            $request['password']=Hash::make($request['identification']);
            $request['user_type']=$userType;
            $request['active']=true;
            $request['organization_id']=$organizationId;
            $userId= User::addUser($request);
            if ($userId == 0){
                return 0;
            }
            return $userId;
        }
        return "busy_credential";
    }

    public static function deleteUser(Request $request, $userId, $userType,$organizationId)
    {
        $result = User::existUserById($userId,$userType,$organizationId);
        if (is_numeric($result) && $result == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($result){
            $result=User::deleteUser($userId);
            if (is_numeric($result) && $result == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            return response()->json(['message'=>self::ok]);
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function availableUser(Request $request, $userId, $userType,$organizationId)
    {
        $existUserByIdentification=User::existUserByIdentification($request['identification'],$userType,$organizationId);
        if (is_numeric($existUserByIdentification) && $existUserByIdentification == 0){
            return 0;
        }
        if ($existUserByIdentification){
            $user =User::getUserByIdentification($request['identification'],$userType,$organizationId);
            if (is_numeric($user) && $user ==0){
                return 0;
            }
            if ($user[0]['id']!=$userId){
                return false;
            }
        }
        $existUserByEmail=User::existUserByEmail($request['email'],$userType,$organizationId);
        if (is_numeric($existUserByEmail) && $existUserByEmail == 0){
            return 0;
        }
        if ($existUserByEmail){
            $user =User::getUserByEmail($request['email'],$userType,$organizationId);
            if (is_numeric($user) && $user ==0){
                return 0;
            }
            if ($user[0]['id']!=$userId){
                return false;
            }
        }
        return true;
    }

    public static function updateUser(Request $request, $userId, $userType,$organizationId)
    {
        self::validate($request);
        $existUserById = User::existUserById($userId,$userType,$organizationId);
        if (is_numeric($existUserById) && $existUserById == 0 ){
            return 0;
        }
        if ($existUserById){
            $availableUser = self::availableUser($request,$userId,$userType,$organizationId);
            if (is_numeric($availableUser) && $availableUser == 0){
                return 0;
            }
            if (!$availableUser){
                return "busy_credential";
            }
            $user=User::getUserById($userId,$userType,$organizationId);
            if (is_numeric($user)&&$user == 0){
                return 0;
            }
            if(isset($user['administrator']) && $user['administrator']['principal']){
                $request['status']=true;
            }
            $request['password']=$user[0]['password'];
            $request['user_type']=$userType;
            $result = User::updateUser($userId,$request);
            if (is_numeric($result) && $result == 0){
                return 0;
            }
            return $userId;
        }
        return "not_found";
    }

    public static function activeUsers(Request $request,$userType,$organizationId)
    {
        $users = User::getUsersActive($userType,$organizationId);
        if (is_numeric($users) && $users == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($users)>0){
            return $users;
        }
        return response()->json(['message'=>self::notFoundActiveUser],206);
    }

    public static function changeUserData(Request $request,$organizationId)
    {
        $user=User::getUserById(auth()->payload()['user']->id,auth()->payload()['user']->user_type,$organizationId);
        if (is_numeric($user) && $user ==0){
            return response()->json(['message'=>self::taskError],206);
        }
        $user=$user[0];
        $availableUser = self::availableUser($request,$user['id'],$user['user_type'],$organizationId);
        if (is_numeric($availableUser) && $availableUser == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!$availableUser){
            return response()->json(['message'=>self::busyCredential],206);
        }
        $request['organization_id']=$organizationId;
        $request['password']=$user['password'];
        $request['user_type']=$user['user_type'];
        $request['activate']=$user['activate'];
        $result = User::updateUser(auth()->payload()['user']->id,$request);
        if (is_numeric($result) && $result ==0){
            return response()->json(['message'=>self::taskError],206);
        }
        return response()->json(['message'=>self::ok],200);
    }

    public static function validateChangePassword(Request $request)
    {
        $request->validate([
            'old_password'=>'required',
            'password'=>'required|confirmed',
        ]);
    }

    public static function changePassword(Request $request,$organizationId)
    {
        self::validateChangePassword($request);
        $user=User::getUserById(auth()->payload()['user']->id,auth()->payload()['user']->user_type,$organizationId);
        if (is_numeric($user)&&$user==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!Hash::check($request['old_password'],$user[0]['password'])){
            return response()->json(['message'=>self::invalidNewPassword],206);
        }
        if ($request['old_password']==$request['password']){
            return response()->json(['message'=>self::invalidPassword],206);
        }
        $user=$user->toArray();
        $user=$user[0];
        $user['password']=Hash::make($request['password']);
        if ($user['user_type']=='A'){
            unset($user['administrator']);
        } else if ($user['user_type']=='T'){
            unset($user['teacher']);
        } else {
            unset($user['student']);
        }
        $result = User::updateUserLikeArray(auth()->payload()['user']->id,$user);
        if (is_numeric($result) && $result ==0){
            return response()->json(['message'=>self::taskError],206);
        }
        return response()->json(['message'=>self::ok],200);
    }
}
