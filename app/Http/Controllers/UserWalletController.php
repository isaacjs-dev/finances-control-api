<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\UserWallet;

class UserWalletController extends Controller
{
    private $loggedUser;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function createUserWallet(Request $req)
    {
        $userWallet = new UserWallet();
        $response['error']['status'] = false;

        $userWallet->id_user = $req->id_user;
        $userWallet->id_wallet = $req->id_wallet;
        $userWallet->proprietary = -1;
        $userWallet->permissions = $req->permissions;

        $userWallet->save();

        return response()->json($response);
    }
    //Falta fazer >>--------<<
    public function updateUserWallet(Request $req, $id)
    {
        $response['error']['status'] = false;

        $userWalletExit = UserWallet::select(
            'UserWallets.id',
            'UserWallets.name',
            'UserWallets.description',
            'users_UserWallets.proprietary',
            'users_UserWallets.permissions'
        )
            ->leftJoin("users_UserWallets", "UserWallets.id", "=", "users_UserWallets.id_UserWallet")
            ->where('UserWallets.id', $id)
            ->where('UserWallets.deleted', -1)
            ->whereNull('UserWallets.deleted_at')
            ->where('users_UserWallets.id_user', $this->loggedUser->id)
            ->where('users_UserWallets.permissions', '<>', 'DENY')
            ->where('users_UserWallets.deleted', -1)
            ->whereNull('users_UserWallets.deleted_at')
            ->first();

        if (!$userWalletExit) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Carteira não existe';
            return response()->json($response);
        };

        if ($userWalletExit->permissions !== 'ALL') {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Você não tem permissão para alterar essa carteira';
            return response()->json($response);
        }


        $userWallet = UserWallet::find($id);

        if ($req->name) {
            $userWallet->name = $req->name;
        }

        if ($req->description) {
            $userWallet->description = $req->description;
        }

        $userWallet->save();
        return response()->json($response);
    }

    public function deleteUserWallet($id)
    {
        $response['error']['status'] = false;

        $userWallet = UserWallet::where('deleted', -1)->whereNull('deleted_at')->find($id);

        if ($userWallet) {
            if ($userWallet->proprietary === $this->loggedUser->id) {
                $response["error"]['status']  = true;
                $response["error"]['messeger']  = 'Você não pode exluir um compartinhamento do qual é o proprietario';
                return response()->json($response);
            }

            if ($userWallet->proprietary === $userWallet->id_user) {
                $response["error"]['status']  = true;
                $response["error"]['messeger']  = 'Você não pode exluir o proprietario da carteira';
                return response()->json($response);
            }

            $userWallet->deleted = 0;
            $userWallet->deleted_at = now();
            $userWallet->save();

            $response["error"]['messeger']  = 'Carteira Exluida';
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Carteira não Encontrada';
        }

        return response()->json($response);
    }
}
