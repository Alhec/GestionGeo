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

    const taskError = 'No se puede proceder con la tarea';
    const busyCredential = 'Identificacion o Correo ya registrados';
    const notFoundUser = 'Usuario no encontrado';
    const taskPartialError = 'No se pudo proceder con la tarea en su totalidad';
    const notSendEmail = 'No se pudo enviar el correo electronico';
    const noAction = "No esta permitido realizar esa accion";
    const unauthorized = 'Unauthorized';
    const notDeletePrincipal = 'Debe designar otro coordinador principal para poder eliminar este usuario';
    const noHasPrincipal = 'No hay coordinador principal';

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
        $user =UserService::addUser($request,'A');
        if ($user=="busy_credential") {
            return response()->json(['message' => self::busyCredential], 206);
        }else if(is_numeric($user) && $user==0){
            return response()->json(['message' => self::taskError], 206);
        }else{
            if (!isset($request['principal'])){
                $request['principal']=false;
            }
            if ($request['principal']){
                if (((auth()->payload()['user']->administrator->principal) ==false &&
                        auth()->payload()['user']->administrator->rol=='COORDINATOR' )||
                    auth()->payload()['user']->administrator->rol=='SECRETARY'){
                    return response()->json(['message'=>self::unauthorized],401);
                }
                $result = Administrator::updateAdministrator(auth()->payload()['user']->id, [
                    'id'=>auth()->payload()['user']->id,
                    'rol'=>auth()->payload()['user']->administrator->rol,
                    'principal'=>false
                ]);
                if(is_numeric($result) && $result==0){
                    return response()->json(['message' => self::taskError], 206);
                }
            }
            $result = Administrator::addAdministrator([
                'id'=>$user,
                'rol'=>$request['rol'],
                'principal'=>$request['principal']
            ]);
            if(is_numeric($result) && $result==0){
                return response()->json(['message' => self::taskError], 206);
            }
            $result = EmailService::userCreate($user,$request->header('organization_key'),'S');
            if ($result==0){
                return response()->json(['message'=>self::notSendEmail],206);
            }
            return UserService::getUserById($request,$user,'A');
        }
    }

    public static function updateAdministrator(Request $request, $id)
    {
        self::validate($request);
        $result =UserService::updateUser($request,$id,'A');
        if ($result=="not_found"){
            return response()->json(['message'=>self::notFoundUser],206);
        }else if (is_numeric($result)&& $result==0){
            return response()->json(['message'=>self::taskError],206);
        }else if ($result=="busy_credential"){
            return response()->json(['message'=>self::busyCredential],206);
        }else {
            if (!isset($request['principal'])){
                $request['principal']=false;
            }
            if ($request['principal']){
                if (((auth()->payload()['user']->administrator->principal) ==false &&
                        auth()->payload()['user']->administrator->rol=='COORDINATOR' )||
                    auth()->payload()['user']->administrator->rol=='SECRETARY'){
                    return response()->json(['message'=>'Unauthorized'],401);
                }
                if(auth()->payload()['user']->id == $id){
                    return response()->json(['message'=>self::noAction],206);
                }
                $result = Administrator::updateAdministrator(auth()->payload()['user']->id, [
                    'id'=>auth()->payload()['user']->id,
                    'rol'=>auth()->payload()['user']->administrator->rol,
                    'principal'=>false
                ]);
                if (is_numeric($result)&& $result==0){
                    return response()->json(['message'=>self::taskError],206);
                }
            }
            $result=Administrator::updateAdministrator($id, [
                'id'=>$id,
                'rol'=>$request['rol'],
                'principal'=>$request['principal']
            ]);
            if (is_numeric($result)&& $result==0){
                return response()->json(['message'=>self::taskError],206);
            }
            return UserService::getUserById($request, $id, 'A');
        }
    }

    public static function deleteAdministrator(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $administrator = User::getUserById($id,'A',$organizationId);
        if (is_numeric($administrator)&&$administrator==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($administrator)<=0){
            return response()->json(['message'=>self::notFoundUser],206);
        }
        if ($administrator[0]['administrator']['principal'] && $administrator[0]['administrator']['rol']=='COORDINATOR'){
            return response()->json(['message'=>self::notDeletePrincipal],206);
        }
        return UserService::deleteUser($request,$id,'A');
    }

    public static function getPrincipalCoordinator(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $administrator = Administrator::getPrincipalCoordinator($organizationId);
        if (is_numeric($administrator)&&$administrator==0){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($administrator)>0){
            return $administrator[0];
        }
        return response()->json(['message'=>self::noHasPrincipal],206);
    }
}
