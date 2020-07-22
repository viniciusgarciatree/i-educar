<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @inheritdoc
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'login' => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'As senhas não são iguais.',
            'password.min' => 'A senha deve conter ao menos 8 caracteres.',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'login', 'password', 'password_confirmation', 'token'
        );
    }
}
