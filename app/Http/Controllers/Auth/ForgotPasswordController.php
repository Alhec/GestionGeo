<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Log;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Mockery\Generator\StringManipulation\Pass\Pass;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    const taskError = 'No se puede proceder con la tarea';
    const invalidUser ='Usuario Invalido';
    const invalidPass = 'Clave Invalida';
    const invalidToken = 'Token Invalido';
    const sendEmail = 'Correo enviado';
    const notSendEmail = 'No se pudo enviar el correo';
    const logUserRequestRecoverPass = 'Solicitud de recuperacion de clave';
    const logSendEmailRecoverPass = 'Correo enviado para recuperacion de clave';
    const logNotSendEmailRecoverPass = 'Correo no enviado para recuperacion de clave';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }



    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email','user_type' => 'required|max:1|ends_with:A,S,T']);
        $request['organization_id'] = $request->header('Organization-Key');
        $user = User::getUserByEmail($request['email'],$request['user_type'],$request['organization_id']);
        if (is_numeric($user) && $user==0){
            return response()->json(['message'=>self::taskError],401);
        }
        try{
            $response = $this->broker()->sendResetLink(
                $request->only('email','user_type','organization_id')
            );
            if (count($user)>0){
                $log = Log::addLog($user[0]['id'],self::logUserRequestRecoverPass);
                if (is_numeric($log) && $log==0){
                    return response()->json(['message'=>self::taskError],206);
                }
            }
            switch ($response) {
                case \Password::INVALID_USER:
                    return response()->json(['message'=>self::invalidUser], 422);
                    break;
                case \Password::INVALID_TOKEN:
                    return response()->json(['message'=> self::invalidToken], 422);
                    break;
                default:
                    $log = Log::addLog($user[0]['id'],self::logSendEmailRecoverPass);
                    if (is_numeric($log) && $log==0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                    return response()->json(['message'=>self::sendEmail], 200);
            }
        }catch(\Exception $e){
            $log = Log::addLog($user[0]['id'],self::logNotSendEmailRecoverPass);
            if (is_numeric($log) && $log==0){
                return response()->json(['message'=>self::taskError],206);
            }
            return response()->json(['message'=>self::notSendEmail], 206);
        }
    }
}
