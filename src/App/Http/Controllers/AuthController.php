<?php

namespace InWeb\Admin\App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
        $id = $request->input('id');
        $password = $request->input('password');
        $remember = $request->input('remember') == 'true';
        $redirect = $request->input('redirect');

        if (
            ! Auth::attempt(['login' => $id, 'password' => $password], $remember) and
            ! Auth::attempt(['email' => $id, 'password' => $password], $remember)
        ) {
            return abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('Неверный логин или пароль'));
        }

        return [
            'user' => Auth::user(),
            'redirect' => '/'
        ];
    }

    public function logout()
    {
        Auth::logout();
    }
}
