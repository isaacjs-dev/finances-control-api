<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Inflow;

class InflowController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    /*
        ATENÇÃO: Isaac do futuro ficou para você a tarefa de verifica se
        o usuário tem premissão para adcionar novos valores
        Dica: where('users_wallets.permissions', '<>', 'DENY'),

        Boa Sorte, seu lindo...
    */
    public function createInflow(Request $req)
    {
        $response['error']['status'] = false;

        $validator = Validator::make($req->all(), [
            'id_wallet' => 'required|integer',
            'id_category' => 'required|integer',
            'value' => 'required|numeric',
            'description' => 'required|string|min:2',
            'expected' => 'required|boolean',
            'frequency' => 'required|boolean',
            'date' => 'required|date',
        ]);

        if (!$validator->fails()) {

            $inflow = new Inflow();
            $inflow->id_wallet = $req->id_wallet;
            $inflow->id_category = $req->id_category;
            $inflow->value = $req->value;
            $inflow->description = $req->description;
            $inflow->expected = $req->expected;
            $inflow->frequency = $req->frequency;
            $inflow->date = $req->date;

            $inflow->save();
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Dados incorretos.';
        }

        return response()->json($response);
    }

    public function readAllInflows()
    {
        $response['error']['status'] = false;



        $response['inflows'] = Inflow::select(
            'inflows.id',
            'inflows.id_wallet',
            'inflows.id_category',
            'inflows.value',
            'inflows.description',
            'inflows.expected',
            'inflows.frequency',
            'inflows.date'
        )
            ->leftJoin("wallets", "inflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('inflows.deleted', -1)
            ->whereNull('inflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')

            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readAllInflowsWallet($idWallet)
    {
        $response['error']['status'] = false;



        $response['inflows'] = Inflow::select(
            'inflows.id',
            'inflows.id_wallet',
            'inflows.id_category',
            'inflows.value',
            'inflows.description',
            'inflows.expected',
            'inflows.frequency',
            'inflows.date'
        )
            ->leftJoin("wallets", "inflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('inflows.deleted', -1)
            ->whereNull('inflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')

            ->where('wallets.id', $idWallet)
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readInflow($id)
    {
        $response['error']['status'] = false;

        $response['inflow'] = Inflow::select(
            'inflows.id',
            'inflows.id_wallet',
            'inflows.id_category',
            'inflows.value',
            'inflows.description',
            'inflows.expected',
            'inflows.frequency',
            'inflows.date'
        )
            ->leftJoin("wallets", "inflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('inflows.id', $id)
            ->where('inflows.deleted', -1)
            ->whereNull('inflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->first();

        return response()->json($response);
    }

    public function updateInflow(Request $req, $id)
    {
        $response['error']['status'] = false;
        $inflowExist = Inflow::select(
            'inflows.id',
            'inflows.id_wallet',
            'inflows.id_category',
            'inflows.value',
            'inflows.description',
            'inflows.expected',
            'inflows.frequency',
            'inflows.date',
            'users_wallets.permissions'
        )
            ->leftJoin("wallets", "inflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('inflows.id', $id)
            ->where('inflows.deleted', -1)
            ->whereNull('inflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->first();

        if (!$inflowExist) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Entrada não existe';
            return response()->json($response);
        }

        if ($inflowExist->permissions !== 'ALL') {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Você não tem permissão para alterar essa carteira';
            return response()->json($response);
        }

        $inflow = Inflow::find($id);

        if ($req->id_category) {
            $inflow->id_category = $req->id_category;
        }

        if ($req->value) {
            $inflow->value = $req->value;
        }

        if ($req->description) {
            $inflow->description = $req->description;
        }

        if ($req->expected) {
            $inflow->expected = $req->expected;
        }

        if ($req->frequency) {
            $inflow->frequency = $req->frequency;
        }

        if ($req->date) {
            $inflow->date = $req->date;
        }

        $inflow->save();
        return response()->json($response);
    }

    public function deleteInflow($id)
    {

        $response['error']['status'] = false;
        $inflowExist = Inflow::select(
            'inflows.id',
            'inflows.id_wallet',
            'inflows.id_category',
            'inflows.value',
            'inflows.description',
            'inflows.expected',
            'inflows.frequency',
            'inflows.date',
            'users_wallets.permissions'
        )
            ->leftJoin("wallets", "inflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('inflows.id', $id)
            ->where('inflows.deleted', -1)
            ->whereNull('inflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->first();

        if (!$inflowExist) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Entrada não existe';
            return response()->json($response);
        }

        if ($inflowExist->permissions !== 'ALL') {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Você não tem permissão para alterar essa carteira';
            return response()->json($response);
        }

        $inflow = Inflow::find($id);
        $inflow->deleted = 0;
        $inflow->deleted_at = now();
        $inflow->save();

        return response()->json($response);
    }
}
