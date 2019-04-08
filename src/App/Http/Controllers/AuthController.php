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
        $remember = $request->input('remember');
        $redirect = $request->input('redirect');

        if (
            ! Auth::attempt(['login' => $id, 'password' => $password], $remember) and
            ! Auth::attempt(['email' => $id, 'password' => $password], $remember)
        ) {
            return abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('Неверный логин или пароль'));
        }

        if (! Auth::user()->isAdmin()) {
            Auth::logout();

            return abort(Response::HTTP_FORBIDDEN, __('У вас недостаточно прав'));
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
