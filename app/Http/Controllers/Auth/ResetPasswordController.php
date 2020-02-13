<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            'user_type'=>'required|max:1|ends_with:A,S,T',
            'email' => 'required|max:30|email',
            'password' => 'required|confirmed',
        ];
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            'email','user_type', 'password', 'password_confirmation', 'token'
        );
    }

    public function reset(Request $request)
    {
        $request->validate( [
            'token' => 'required',
            'user_type'=>'required|max:1|ends_with:A,S,T',
            'email' => 'required|max:30|email',
            'password' => 'required|confirmed',
        ]);

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == \Password::PASSWORD_RESET
            ? response()->json(['message'=>'Clave reseteada'], 200)
                    : response()->json(['message'=>'Token invalido o vencido'], 422);
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();
    }
}
