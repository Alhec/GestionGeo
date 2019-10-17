<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 27/08/19
 * Time: 02:24 PM
 */

namespace App\Services;


use App\Organization;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\OrganizationUser;

class UserService
{
    public static function getUsers(Request $request, $userType)
    {
        $organizationId = $request->header('organization_key');
        $users= User::getUsers($userType,$organizationId);
        if (count($users)>0){
            return $users;
        }
        return response()->json(['message'=>'No existen usuarios con ese perfil'],206);
    }

    public static function getUserById(Request $request, $userId, $userType)
    {
        $organizationId = $request->header('organization_key');
        $user = User::getUserById($userId,$userType,$organizationId);
        if (count($user)>0){
            return $user[0];
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
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
            'level_instruction'=>'max:3|ends_with:TCU,TCM,Dr,Esp,Ing,MSc,Lic',
            'with_work'=>'boolean',
            'with_disabilities'=>'boolean',
            'sex'=>'required|max:1|ends_with:M,F',
            'nationality'=>'required|max:1|ends_with:V,E',
        ]);
    }

    public static function addUser(Request $request,$userType)
    {
        $organizationId = $request->header('organization_key');
        self::validate($request);
        if (Organization::existOrganization($organizationId)){
            if (!(User::existUserByIdentification($request['identification'],$userType,$organizationId))AND!(User::existUserByEmail($request['email'],$userType,$organizationId))){
                $request['password']=Hash::make($request['identification']);
                $request['user_type']=$userType;
                $request['active']=true;
                $userId= User::addUser($request);
                OrganizationUser::addOrganizationUser([
                    'user_id'=>$userId,
                    'organization_id'=>$organizationId,
                ]);
                //EmailService::userCreate($userId,$organizationId,$userType);
                return $userId;
            }
            return "identification_email";
        }
        return "organization";
    }

    public static function deleteUser(Request $request, $userId, $userType)
    {
        $organizationId = $request->header('organization_key');
        if (User::existUserById($userId,$userType,$organizationId)){
            User::deleteUser($userId);
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }

    public static function availableUser(Request $request, $userId, $userType,$organizationId)
    {
        if (User::existUserByIdentification($request['identification'],$userType,$organizationId)){
            if (User::getUserByIdentification($request['identification'],$userType,$organizationId)[0]['id']!=$userId){
                return false;
            }
        }
        if (User::existUserByEmail($request['email'],$userType,$organizationId)){
            if (User::getUserByEmail($request['email'],$userType,$organizationId)[0]['id']!=$userId){
                return false;
            }
        }
        return true;
    }

    public static function updateUser(Request $request, $userId, $userType)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (Organization::existOrganization($organizationId)){
            if (User::existUserById($userId,$userType,$organizationId)){
                if (!self::availableUser($request,$userId,$userType,$organizationId)){
                    return "identification_email";
                }
                $user=User::getUserById($userId,$userType,$organizationId);
                $request['password']=$user[0]['password'];
                $request['user_type']=$userType;
                User::updateUser($userId,$request);
                return $userId;
            }
            return "user";
        }
        return "organization";
    }

    public static function activeUsers(Request $request,$userType)
    {
        $organizationId = $request->header('organization_key');
        $users = User::getUsersActive($userType,$organizationId);
        if (count($users)>0){
            return $users;
        }
        return response()->json(['message'=>'No existen usuarios activos con ese perfil'],206);

    }

    public function changeUserData(Request $request)
    {
        $organizationId = $request->header('organization_key');
        if (auth()->payload()['user'][0]->id!=$request['id']){
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $user=User::getUserById(auth()->payload()['user'][0]->id,auth()->payload()['user'][0]->user_type,$organizationId)[0];
        $request['password']=$user['password'];
        $request['user_type']=$user['user_type'];
        User::updateUser($request['id'],$request);
        return response()->json(['message'=>'Ok'],200);
    }

    public static function validateChangePassword(Request $request)
    {
        $request->validate([
            'id'=>'required|numeric',
            'old_password'=>'required',
            'password'=>'required|confirmed',
        ]);
    }

    public static function changePassword(Request $request)
    {
        $organizationId = $request->header('organization_key');
        if (auth()->payload()['user'][0]->id!=$request['id']){
            return response()->json(['message'=>'Unauthorized'],401);
        }
        self::validateChangePassword($request);
        $user=User::getUserById(auth()->payload()['user'][0]->id,auth()->payload()['user'][0]->user_type,$organizationId)[0];

        if (!Hash::check($request['old_password'],$user['password'])){
            return response()->json(['message'=>'La clave esta errada'],206);
        }
        $user=$user->toArray();
        $user['password']=Hash::make($request['password']);
        if ($user['user_type']=='A'){
            unset($user['administrator']);
        } else if ($user['user_type']=='T'){
            unset($user['teacher']);
        } else {
            unset($user['student']);
        }
        User::updateUserLikeArray($request['id'],$user);
        return response()->json(['message'=>'Ok'],200);
    }
}
