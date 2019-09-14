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
    public static function clearUser($users)
    {
        $usersReturns =[];
        foreach ($users as $user){
            unset($user['organization']);
            $usersReturns[]=$user;
        }
        return $usersReturns;
    }

    public static function getUser(Request $request, $userType)
    {
        $organizationId = $request->header('organization_key');
        $users= User::getUsers($userType,$organizationId);
        if (count($users)>0){
            return self::clearUser($users);
        }
        return response()->json(['message'=>'No existen usuarios con ese perfil'],206);
    }

    public static function getUserById(Request $request, $userId, $userType)
    {
        $organizationId = $request->header('organization_key');
        $administrator = User::getUserById($userId,$userType,$organizationId);
        if (count($administrator)>0){
            return self::clearUser($administrator)[0];
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
            'mobile'=>'max:15',
            'work_phone'=>'max:15',
            'email'=>'max:30',
            'user_type'=>'max:1|ends_with:A,S,T',
            'level_instruction'=>'max:20',
            'active'=>'boolean',
            'withWork'=>'boolean',
            'withDisabilities'=>'boolean',
        ]);
    }

    public static function getIdUser($identification,$userType) // puede ocurrir el caso en queun estudiante este en mas de un postgrado
    {
        $users=User::getUsersByIdentification($identification,$userType);
        foreach ($users as $user){
            if (!OrganizationUser::existOrganizationUser($user['id'])){
                return $user['id'];
            }
        }
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
                User::addUser($request);
                $userId= self::getIdUser($request['identification'],$userType);
                OrganizationUser::addOrganizationUser([
                    'user_id'=>$userId,
                    'organization_id'=>$organizationId,
                ]);
                return self::getUserById($request,$userId,$userType);
            }
            if ($userType =='S'||$userType =='T'){
                return 1;//Para service de profesor y estudiante errores
            }
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }
        if ($userType =='S'||$userType =='T'){
            return 2;//Para service de profesor y estudiante errores
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);
    }

    public static function deleteUser(Request $request, $userId, $userType)
    {
        $organizationId = $request->header('organization_key');
        if (count(User::getUserById($userId,$userType,$organizationId))>0){
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
        $user=User::getUserById($userId,$userType,$organizationId);
        if (count($user)>0){
            if (!self::availableUser($request,$userId,$userType,$organizationId)){
                return response()->json(['message'=>'Identificacion o correo ya registrado'],206);
            }
            $request['password']=$user[0]['password'];
            $request['user_type']=$userType;
            User::updateUser($userId,$request);
            return self::getUserById($request,$userId,$userType);
        }
        if ($userType =='S'||$userType =='T'){
            return 3;//Para service de profesor y estudiante errores
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }

    public static function activeUser(Request $request,$userType)
    {
        $organizationId = $request->header('organization_key');
        $users = User::getUsersActive($userType,$organizationId);
        if (count($users)>0){
            return self::clearUser($users);
        }
        return response()->json(['message'=>'No existen usuarios activos con ese perfil'],206);

    }
}
