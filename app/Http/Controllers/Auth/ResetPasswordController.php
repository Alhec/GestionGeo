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

    // Comentamos esto que no hace falta
    // public function __construct()
    // {
    //     $this-&gt;middleware('guest');
    // }

    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $response = $this->broker()->reset(
        $this->credentials($request), function ($user, $password) {
        $this->resetPassword($user, $password);
            }
        );

        return $response == \Password::PASSWORD_RESET
            ? response()->json($response, 200)
                    : response()->json($response, 422);
    }

    protected function resetPassword($user, $password)
    {
        /*$user->forceFill([
        'password' => $password,
            'remember_token' => str_random(60),
        ])->save();*/
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();

        // GENERAR TOKEN PARA SATELLIZER AQUI ??
        // $this-&gt;guard()-&gt;login($user);
    }
}
