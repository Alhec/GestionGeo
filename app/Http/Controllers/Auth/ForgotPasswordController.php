<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    //use SendsPasswordResetEmails;

    // Comentamos esto que no nos hace falta
    // public function __construct()
    // {
    //     $this-&gt;middleware('guest');
    // }

    // Añadimos las respuestas JSON, ya que el Frontend solo recibe JSON


    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email','user_type' => 'required|max:1|ends_with:A,S,T']);
        $request['organization_id'] = $request->header('Organization-Key');
        try{
            $response = $this->broker()->sendResetLink(
                $request->only('email','user_type','organization_id')
            );
            switch ($response) {
                case \Password::INVALID_USER:
                    return response()->json(['message'=>'Usuario Invalido'], 422);
                    break;
                case \Password::INVALID_PASSWORD:
                    return response()->json(['message'=>'Clave Invalida'], 422);
                    break;
                case \Password::INVALID_TOKEN:
                    return response()->json(['message'=>'Token Invalido'], 422);
                    break;
                default:
                    return response()->json(['message'=>'Correo enviado'], 200);
            }
        }catch(\Exception $e){
            return response()->json(['message'=>'No se pudo enviar el correo'], 206);
        }
    }
}
