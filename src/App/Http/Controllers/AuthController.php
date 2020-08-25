<?php

namespace InWeb\Admin\App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InWeb\Admin\App\Admin;

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

        $data = [
            'user' => Auth::user(),
        ];

        if (strpos($redirect, Admin::path()) === 0)
            $data['redirect'] = trim(str_replace(Admin::path(), '', $redirect), '/');
        else
            $data['fullRedirect'] = '/' . $redirect;

        return $data;
    }

    public function logout()
    {
        Auth::logout();
    }
}
