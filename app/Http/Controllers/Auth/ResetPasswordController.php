<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Log;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    const taskError = 'No se puede proceder con la tarea';
    const emailNotFound = 'Correo no encontrado';
    const resetPassword = 'Clave reseteada';
    const notResetPassword = 'Token invalido o vencido';

    const logResetPasword = 'Reseteo su clave';
    const logNotResetPasword = 'Provee token invalido';
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|max:30|email',
            'password' => 'required|confirmed',
        ];
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    public function reset(Request $request)
    {
        $request->validate( [
            'token' => 'required',
            'email' => 'required|max:30|email',
            'password' => 'required|confirmed',
        ]);
        $request['organization_id'] = $request->header('Organization-Key');
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );
        $user = User::getUserByEmail($request['email'],$request['organization_id']);
        if (is_numeric($user) && $user==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($user)>0){
            if($response == \Password::PASSWORD_RESET){
                $log = Log::addLog($user[0]['id'],self::logResetPasword);
                if (is_numeric($log) && $log==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                return response()->json(['message'=>self::resetPassword], 200);
            };
            $log = Log::addLog($user[0]['id'],self::logNotResetPasword);
            if (is_numeric($log) && $log==0){
                return response()->json(['message'=>self::taskError],206);
            }
            return response()->json(['message'=>self::notResetPassword], 422);
        }
        return response()->json(['message'=>self::emailNotFound], 206);
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();
    }
}
