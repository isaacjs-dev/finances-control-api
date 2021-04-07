<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['createUser']]);
        $this->loggedUser = Auth::user();
    }

    public function createUser(Request $req)
    {
        $response['error']['status'] = false;

        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required',
        ]);

        if (!$validator->fails()) {
            if (!User::where('email', $req->email)->exists()) {
                $user = new User();
                $user->name = $req->name;
                $user->email = $req->email;
                $user->password = password_hash($req->password, PASSWORD_DEFAULT);
                $user->phone = $req->phone;

                $user->save();

                $token = Auth::attempt([
                    'email' => $req->email,
                    'password' => $req->password
                ]);

                if (!$token) {
                    $response["error"]['status']  = true;
                    $response["error"]['messeger']  = 'Erro ao tentar logar!';
                }

                $response["user"]['data'] = Auth::user();
                $response["user"]['token'] = $token;
            } else {
                $response["error"]['status']  = true;
                $response["error"]['messeger']  = 'Email já cadastrado!';
            }
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Dados incorretos.';
        }

        return response()->json($response);
    }

    /***** Nivel de Usuarios */
    public function readAllUsers()
    {
        $response['error']['status'] = false;

        $response['cards'] = User::select('id', 'name', 'email', 'phone')
            ->where('deleted', -1)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($response);
    }

    /***** Nivel de Usuarios */
    public function readUser($id)
    {
        $response['error']['status'] = false;

        $response['card'] = User::where('deleted', -1)
            ->whereNull('deleted_at')
            ->find($id, ['id', 'name', 'email', 'phone']);

        return response()->json($response);
    }

    public function updateUser(Request $req)
    {
        $userId = $this->loggedUser->id;

        $response['error']['status'] = false;

        $rules = [
            'name' => 'min:2',
            'email' => 'email|unique:users',
            'password' => 'same:password_confirm',
            'password_confirm' => 'same:password',
            'phone' => 'min:8'
        ];

        $validator = Validator::make($req->all(), $rules);

        if ($validator->fails()) {
            $response['error']['status'] = false;
            $response['error']['message'] = $validator->messages();
            return response()->json($response);
        }

        if (User::where('deleted', -1)->whereNull('deleted_at')->find($userId)) {

            $user = User::find($userId);

            if ($req->name) {
                $user->name = $req->name;
            }

            if ($req->email) {
                $user->email = $req->email;
            }

            if ($req->password) {
                $user->password = password_hash($req->password, PASSWORD_DEFAULT);
            }

            if ($req->phone) {
                $user->phone = $req->phone;
            }

            $user->save();

            $response["user"]  = Auth::user();
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Usuario não existe';
        }

        return response()->json($response);
    }

    /***** Nivel de Usuarios */
    public function deleteUser($id)
    {
        $response['error']['status'] = false;

        if (User::where('deleted', -1)->whereNull('deleted_at')->find($id)) {
            $user = User::find($id);

            $user->deleted = 0;
            $user->deleted_at = now();
            $user->save();

            $response["error"]['messeger']  = 'Usuário exluido';
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Usuário não Encontrado';
        }

        return response()->json($response);
    }
}
