<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Wallet;
use App\Models\UserWallet;

class WalletController extends Controller
{
    private $loggedUser;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function createWallet(Request $request)
    {
        $wallet = new Wallet();
        $response['error']['status'] = false;

        $name = Trim(preg_replace('/\\s\\s+/', ' ',  $request->name));
        $description = Trim(preg_replace('/\\s\\s+/', ' ',  $request->description));

        if ($description !== '') {
            $wallet->name = $name;
            $wallet->description = $description;

            if ($wallet->save()) {
                $userWallet = new UserWallet();
                $userWallet->id_user = $this->loggedUser->id;
                $userWallet->id_wallet = $wallet->id;
                $userWallet->proprietary = $this->loggedUser->id;
                $userWallet->permissions = "ALL";

                $userWallet->save();
            };
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Informações sobre o cartão invalidas';
        }

        return response()->json($response);
    }

    public function readAllWallets()
    {
        $response['error']['status'] = false;

        $response['Wallets'] = Wallet::select(
            'wallets.id',
            'wallets.name',
            'wallets.description',
            'users_wallets.proprietary',
            'users_wallets.permissions'
        )
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readWallet($id)
    {
        $response['error']['status'] = false;

        $response['Wallet']  = Wallet::select(
            'wallets.id',
            'wallets.name',
            'wallets.description',
            'users_wallets.proprietary',
            'users_wallets.permissions'
        )
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")
            ->where('wallets.id', $id)
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->first();

        return response()->json($response);
    }

    public function updateWallet(Request $req, $id)
    {
        $response['error']['status'] = false;

        $walletExist = Wallet::select(
            'wallets.id',
            'wallets.name',
            'wallets.description',
            'users_wallets.proprietary',
            'users_wallets.permissions'
        )
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")
            ->where('wallets.id', $id)
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->first();

        if (!$walletExist) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Carteira não existe';
            return response()->json($response);
        };

        if ($walletExist->permissions !== 'ALL') {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Você não tem permissão para alterar essa carteira';
            return response()->json($response);
        }

        $wallet = Wallet::find($id);

        if ($req->name) {
            $wallet->name = $req->name;
        }

        if ($req->description) {
            $wallet->description = $req->description;
        }

        $wallet->save();
        return response()->json($response);
    }

    public function deleteWallet($id)
    {
        $response['error']['status'] = false;

        $walletExit = Wallet::select(
            'wallets.id',
            'wallets.name',
            'wallets.description',
            'users_wallets.proprietary',
            'users_wallets.permissions'
        )
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")
            ->where('wallets.id', $id)
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->first();

        if (!$walletExit) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Carteira não existe';
            return response()->json($response);
        };

        if ($walletExit->proprietary !== $this->loggedUser->id) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Você é o proprietario dessa carteira por isso não tem permissão para excluí-la';
            return response()->json($response);
        }

        $wallet = Wallet::find($id);
        $wallet->deleted = 0;
        $wallet->deleted_at = now();
        $wallet->save();

        $userWallets = UserWallet::where('id_wallet', $id)->get();
        foreach ($userWallets as $userWallet) {
            $userWallet->deleted = 0;
            $userWallet->deleted_at = now();
            $userWallet->save();
        }

        $response["error"]['messeger']  = 'Carteira Exluida';

        return response()->json($response);
    }
}
