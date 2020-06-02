<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 03/10/19
 * Time: 04:30 PM
 */

namespace App\Services;

use App\Log;
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
    const unauthorized = 'Unauthorized';
    const notDeletePrincipal = 'Debe designar otro coordinador principal para poder eliminar este usuario, solo el coordinador principal puede realizar esta accion';
    const hasNotPrincipal = 'No hay coordinador principal';

    const logCreateAdmin = 'Creo la entidad administrator para ';
    const logUpdateAdmin = 'Actualizo la entidad administrator para ';
    public static function validate(Request $request)
    {
        $request->validate([
            'rol'=>'required|max:11|ends_with:COORDINATOR,SECRETARY',
            'principal'=>'boolean'
        ]);
    }

    public static function addAdministrator(Request $request,$organizationId)
    {
        self::validate($request);
        if ($request['principal']&& $request['rol']=='COORDINATOR'){
            if (((auth()->payload()['user']->administrator->principal) ==false &&
                    auth()->payload()['user']->administrator->rol=='COORDINATOR' )||
                auth()->payload()['user']->administrator->rol=='SECRETARY'){
                return response()->json(['message'=>self::unauthorized],401);
            }
        }
        $user = UserService::addUser($request,'A',$organizationId);
        if ($user=="busy_credential") {
            return response()->json(['message' => self::busyCredential], 206);
        }else if(is_numeric($user) && $user==0){
            return response()->json(['message' => self::taskError], 206);
        }else{
            if (!isset($request['principal'])){
                $request['principal']=false;
            }
            if ($request['principal']&& $request['rol']=='COORDINATOR'){
                $result = Administrator::updateAdministrator(auth()->payload()['user']->id, [
                    'id'=>auth()->payload()['user']->id,
                    'rol'=>auth()->payload()['user']->administrator->rol,
                    'principal'=>false
                ]);
                if(is_numeric($result) && $result==0){
                    return response()->json(['message' => self::taskError], 206);
                }
            }
            if ($request['rol']=='COORDINATOR'){
                $result = Administrator::addAdministrator([
                    'id'=>$user,
                    'rol'=>$request['rol'],
                    'principal'=>$request['principal']
                ]);
            }else{
                $result = Administrator::addAdministrator([
                    'id'=>$user,
                    'rol'=>$request['rol'],
                    'principal'=>false
                ]);
            }
            if(is_numeric($result) && $result==0){
                return response()->json(['message' => self::taskPartialError], 206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logCreateAdmin.$request['first_name'].
                ' '.$request['first_surname']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskPartialError],401);
            }
            $result = EmailService::userCreate($user,$organizationId,'A');
            if ($result==0){
                return response()->json(['message'=>self::notSendEmail],206);
            }
            return UserService::getUserById($user,'A',$organizationId);
        }
    }

    public static function updateAdministrator(Request $request, $id,$organizationId)
    {
        self::validate($request);
        if ($request['principal']&& $request['rol']=='COORDINATOR'){
            $request['active']=true;
            if (((auth()->payload()['user']->administrator->principal) ==false &&
                    auth()->payload()['user']->administrator->rol=='COORDINATOR' )||
                auth()->payload()['user']->administrator->rol=='SECRETARY'){
                return response()->json(['message'=>self::unauthorized],401);
            }
        }
        $result =UserService::updateUser($request,$id,'A',$organizationId);
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
            if ($request['principal'] && $request['rol']=='COORDINATOR' ){
                if(auth()->payload()['user']->id != $id){
                    $result = Administrator::updateAdministrator(auth()->payload()['user']->id, [
                        'id'=>auth()->payload()['user']->id,
                        'rol'=>auth()->payload()['user']->administrator->rol,
                        'principal'=>false
                    ]);
                    if (is_numeric($result)&& $result==0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                }
            }
            if ($request['rol']=='COORDINATOR'){
                $result=Administrator::updateAdministrator($id, [
                    'id'=>$id,
                    'rol'=>$request['rol'],
                    'principal'=>$request['principal']
                ]);
            }else{
                $result=Administrator::updateAdministrator($id, [
                    'id'=>$id,
                    'rol'=>$request['rol'],
                    'principal'=>false
                ]);
            }
            if (is_numeric($result)&& $result==0){
                return response()->json(['message'=>self::taskPartialError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logUpdateAdmin.$request['first_name'].
                ' '.$request['first_surname']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskPartialError],401);
            }
            return UserService::getUserById($id, 'A',$organizationId);
        }
    }

    public static function deleteAdministrator($id,$organizationId)
    {
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
        return UserService::deleteUser($id,'A',$organizationId);
    }

    public static function getPrincipalCoordinator($organizationId,$internalCall)
    {
        $administrator = Administrator::getPrincipalCoordinator($organizationId);
        if (is_numeric($administrator)&&$administrator==0){
            if ($internalCall){
                return 0;
            }
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($administrator)>0){
            return $administrator[0];
        }
        if ($internalCall){
            return 'noExist';
        }
        return response()->json(['message'=>self::hasNotPrincipal],206);
    }
}
