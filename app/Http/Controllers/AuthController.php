<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'unauthorized']]);
    }

    public function login(Request $req)
    {
        $response['error']['status'] = false;

        $token = Auth::attempt([
            'email' => $req->email,
            'password' => $req->password
        ]);

        if (!$token) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Usuário e/ou senha errados!';

            return response()->json($response);
        }

        $response["user"]['data'] = Auth::user();
        $response["user"]['token'] = $token;

        return response()->json($response);
    }

    public function logout()
    {
        Auth::logout();

        $response['error']['status'] = false;
        return response()->json($response);
    }

    public function refresh()
    {
        $response['error']['status'] = false;

        $response["user"]['token'] = Auth::refresh();
        $response["user"]['data'] = Auth::user();

        return response()->json($response);
    }

    public function unauthorized()
    {
        $response["error"]['status']  = true;
        $response["error"]['messeger']  = 'Não autorizado';

        return response()->json($response, 401);
    }
}
