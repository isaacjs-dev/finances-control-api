<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Card;

class CardController extends Controller
{
    private $loggedUser;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = Auth::user();
    }

    public function createCard(Request $request)
    {
        $card = new Card();
        $response['error']['status'] = false;

        $description = Trim(preg_replace('/\\s\\s+/', ' ',  $request->description));

        if ($description !== '') {
            $card->description = $description;
            $card->save();
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Informações sobre o cartão invalidas';
        }


        return response()->json($response);
    }

    public function readAllCards()
    {
        $response['error']['status'] = false;

        $response['cards'] = Card::select('id', 'description')
            ->where('deleted', -1)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($response);
    }

    public function readCard($id)
    {
        $response['error']['status'] = false;

        $response['card'] = Card::where('deleted', -1)
            ->whereNull('deleted_at')
            ->find($id, ['id', 'description']);

        return response()->json($response);
    }

    public function updateCard(Request $request, $id)
    {
        $response['error']['status'] = false;

        if (Card::where('deleted', -1)->whereNull('deleted_at')->find($id)) {
            $description = Trim(preg_replace('/\\s\\s+/', ' ',  $request->description));

            if ($description !== '') {
                $card = Card::find($id);
                $card->description = $description;
                $card->save();
            } else {
                $response["error"]['status']  = true;
                $response["error"]['messeger']  = 'Informações sobre o cartão invalidas';
            }
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Cartão não existe';
        }

        return response()->json($response);
    }

    public function deleteCard($id)
    {
        $response['error']['status'] = false;

        if (Card::where('deleted', -1)->whereNull('deleted_at')->find($id)) {
            $card = Card::find($id);

            $card->deleted = 0;
            $card->deleted_at = now();
            $card->save();

            $response["error"]['messeger']  = 'Cartão exluido';
        } else {
            $response["error"]['status']  = true;
            $response["error"]['messeger']  = 'Cartão não Encontrado';
        }

        return response()->json($response);
    }
}
