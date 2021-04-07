<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Outflow;

class OutflowController extends Controller
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
    public function createOutflow(Request $req)
    {
        $response['error']['status'] = false;

        $validator = Validator::make($req->all(), [
            'id_wallet' => 'required|integer',
            'id_category' => 'required|integer',
            'value' => 'required|numeric',
            'description' => 'required|string|min:2',
            'expected' => 'required|boolean',
            'frequency' => 'required|boolean',
            'id_type_pay' => 'required|boolean',
            'id_card' => 'integer',
            'date' => 'required|date',
        ]);

        if (!$validator->fails()) {

            $outflow = new Outflow();
            $outflow->id_wallet = $req->id_wallet;
            $outflow->id_category = $req->id_category;
            $outflow->value = $req->value;
            $outflow->description = $req->description;
            $outflow->expected = $req->expected;
            $outflow->frequency = $req->frequency;
            $outflow->id_type_pay = $req->id_type_pay;
            $outflow->id_card = $req->id_card;
            $outflow->link = $req->link;
            $outflow->date = $req->date;

            $outflow->save();
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Dados incorretos.';
        }

        return response()->json($response);
    }

    public function readAllOutflows()
    {
        $response['error']['status'] = false;

        $response['outflows'] = Outflow::select(
            'outflows.id',
            'outflows.id_wallet',
            'outflows.id_category',
            'outflows.value',
            'outflows.description',
            'outflows.expected',
            'outflows.frequency',
            'outflows.id_type_pay',
            'outflows.id_card',
            'outflows.link',
            'outflows.date'
        )
            ->leftJoin("wallets", "outflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('outflows.deleted', -1)
            ->whereNull('outflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')

            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readAllOutflowsWallet($idWallet)
    {
        $response['error']['status'] = false;

        $response['outflows'] = Outflow::select(
            'outflows.id',
            'outflows.id_wallet',
            'outflows.id_category',
            'outflows.value',
            'outflows.description',
            'outflows.expected',
            'outflows.frequency',
            'outflows.id_type_pay',
            'outflows.id_card',
            'outflows.link',
            'outflows.date'
        )
            ->leftJoin("wallets", "outflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('outflows.deleted', -1)
            ->whereNull('outflows.deleted_at')

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

    public function readOutflow($id)
    {
        $response['error']['status'] = false;


        $response['outflows'] = Outflow::select(
            'outflows.id',
            'outflows.id_wallet',
            'outflows.id_category',
            'outflows.value',
            'outflows.description',
            'outflows.expected',
            'outflows.frequency',
            'outflows.id_type_pay',
            'outflows.id_card',
            'outflows.link',
            'outflows.date'
        )
            ->leftJoin("wallets", "outflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('outflows.id', $id)
            ->where('outflows.deleted', -1)
            ->whereNull('outflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->first();

        return response()->json($response);
    }

    public function updateOutflow(Request $req, $id)
    {
        $response['error']['status'] = false;

        $outflowExist = Outflow::select(
            'outflows.id',
            'outflows.id_wallet',
            'outflows.id_category',
            'outflows.value',
            'outflows.description',
            'outflows.expected',
            'outflows.frequency',
            'outflows.id_type_pay',
            'outflows.id_card',
            'outflows.link',
            'outflows.date',
            'users_wallets.permissions'
        )
            ->leftJoin("wallets", "outflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('outflows.id', $id)
            ->where('outflows.deleted', -1)
            ->whereNull('outflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->first();


        if (!$outflowExist) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Entrada não existe';
            return response()->json($response);
        }

        if ($outflowExist->permissions !== 'ALL') {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Você não tem permissão para alterar essa carteira';
            return response()->json($response);
        }

        $outflow = Outflow::find($id);

        if ($req->id_category) {
            $outflow->id_category = $req->id_category;
        }

        if ($req->value) {
            $outflow->value = $req->value;
        }

        if ($req->description) {
            $outflow->description = $req->description;
        }

        if ($req->expected) {
            $outflow->expected = $req->expected;
        }

        if ($req->frequency) {
            $outflow->frequency = $req->frequency;
        }

        if ($req->date) {
            $outflow->date = $req->date;
        }

        if ($req->id_type_pay) {
            $outflow->id_type_pay = $req->id_type_pay;
        }

        if ($req->id_card) {
            $outflow->id_card = $req->id_card;
        }

        $outflow->save();
        return response()->json($response);
    }

    public function deleteOutflow($id)
    {

        $response['error']['status'] = false;

        $outflowExist = Outflow::select(
            'outflows.id',
            'outflows.id_wallet',
            'outflows.id_category',
            'outflows.value',
            'outflows.description',
            'outflows.expected',
            'outflows.frequency',
            'outflows.id_type_pay',
            'outflows.id_card',
            'outflows.link',
            'outflows.date',
            'users_wallets.permissions'
        )
            ->leftJoin("wallets", "outflows.id_wallet", "=", "wallets.id")
            ->leftJoin("users_wallets", "wallets.id", "=", "users_wallets.id_wallet")

            ->where('outflows.id', $id)
            ->where('outflows.deleted', -1)
            ->whereNull('outflows.deleted_at')

            ->where('users_wallets.deleted', -1)
            ->whereNull('users_wallets.deleted_at')
            ->where('users_wallets.id_user', $this->loggedUser->id)
            ->where('users_wallets.permissions', '<>', 'DENY')
            ->where('wallets.deleted', -1)
            ->whereNull('wallets.deleted_at')
            ->first();

        if (!$outflowExist) {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Entrada não existe';
            return response()->json($response);
        }

        if ($outflowExist->permissions !== 'ALL') {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Você não tem permissão para alterar essa carteira';
            return response()->json($response);
        }

        $outflow = Outflow::find($id);
        $outflow->deleted = 0;
        $outflow->deleted_at = now();
        $outflow->save();

        return response()->json($response);
    }
}
