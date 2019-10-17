<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 03/10/19
 * Time: 04:30 PM
 */

namespace App\Services;

use Illuminate\Http\Request;
use App\Administrator;
use App\User;

class AdministratorService
{
    public static function validate(Request $request)
    {
        $request->validate([
            'rol'=>'required|max:11|ends_with:COORDINATOR,SECRETARY',
            'principal'=>'boolean'
        ]);
    }

    public static function addAdministrator(Request $request)
    {
        self::validate($request);
        $result =UserService::addUser($request,'A');
        if ($result=="identification_email"){
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else if ($result=="organization"){
            return response()->json(['message'=>'No existe organizacion asociada'],206);
        }else{
            if (!isset($request['principal'])){
                $request['principal']=false;
            }
            if ($request['principal']){
                if (((auth()->payload()['user'][0]->administrator->principal) ==false && auth()->payload()['user'][0]->administrator->rol=='COORDINATOR' )|| auth()->payload()['user'][0]->administrator->rol=='SECRETARY'){
                    return response()->json(['message'=>'Unauthorized'],401);
                }
                Administrator::updateAdministrator(auth()->payload()['user'][0]->id, [
                    'user_id'=>auth()->payload()['user'][0]->id,
                    'rol'=>auth()->payload()['user'][0]->administrator->rol,
                    'principal'=>false
                ]);
            }
            Administrator::addAdministrator([
                'user_id'=>$result,
                'rol'=>$request['rol'],
                'principal'=>$request['principal']
            ]);
            return UserService::getUserById($request,$result,'A');
        }
    }

    public static function updateAdministrator(Request $request, $id)
    {
        self::validate($request);
        $result =UserService::updateUser($request,$id,'A');
        if ($result=="user"){
            return response()->json(['message'=>'Usuario no encontrado'],206);
        }else if ($result=="organization"){
            return response()->json(['message'=>'No existe organizacion asociada'],206);
        }else if ($result=="identification_email"){
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else {
            if (!isset($request['principal'])){
                $request['principal']=false;
            }
            if ($request['principal']){
                if (((auth()->payload()['user'][0]->administrator->principal) ==false && auth()->payload()['user'][0]->administrator->rol=='COORDINATOR' )|| auth()->payload()['user'][0]->administrator->rol=='SECRETARY'){
                    return response()->json(['message'=>'Unauthorized'],401);
                }
                Administrator::updateAdministrator(auth()->payload()['user'][0]->id, [
                    'user_id'=>auth()->payload()['user'][0]->id,
                    'rol'=>auth()->payload()['user'][0]->administrator->rol,
                    'principal'=>false
                ]);

            }
            Administrator::updateAdministrator($id, [
                'user_id'=>$id,
                'rol'=>$request['rol'],
                'principal'=>$request['principal']
            ]);
            return UserService::getUserById($request, $id, 'A');
        }
    }

    public static function deleteAdministrator(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $administrator = User::getUserById($id,'A',$organizationId);
        if (count($administrator)<=0){
            return response()->json(['message'=>'Usuario no encontrado'],206);
        }
        if ($administrator[0]['administrator']['principal'] && $administrator[0]['administrator']['rol']=='COORDINATOR'){
            return response()->json(['message'=>'Debe designar otro coordinador principal para poder eliminar este usuario'],206);
        }
        return UserService::deleteUser($request,$id,'A');
    }

    public static function getPrincipalCoordinator(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $administrator = Administrator::getPrincipalCoordinator($organizationId);
        if (count($administrator)>0){
            return $administrator[0];
        }
        return response()->json(['message'=>'No hay coordinador principal'],206);
    }
}
