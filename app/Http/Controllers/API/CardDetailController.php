<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CardDetail;
use Auth;

class CardDetailController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function carddetails(Request $requesaddcardt)
    {
        $user = Auth::user();
        $userid = $user->id;

        $cardlist = CardDetail::where('user_id', $userid)->orderby('updated_at', 'desc')->get();
        $blist = array();

        if (count($cardlist) < 1)
            $response['message'] = 'You have not added any card details';
        else {
            foreach ($cardlist as $card)
                $blist[] =
                    array(
                        'id' => $card->id,
                        'card_number' => $card->card_number,
                        'expire_date' => $card->expire_date,
                        'cvv' => $card->cvv,
                        'card_holder' => $card->card_holder,
                    );
            $response['message'] = 'Success';
        }
        $response['data'] = $blist;
        $response['status'] = "true";
        return response()->json($response);
    }

    public function getcard(Request $request)
    {
        $user = Auth::user();

        try {
            $ban = CardDetail::where("id", $request->card_id)->first();

            $card = array
            (
                'id' => $ban->id,
                'card_number' => $ban->card_number,
                'expire_date' => $ban->expire_date,
                'cvv' => $ban->cvv,
                'card_holder' => $ban->card_holder
            );

            $response['status'] = "true";
            $response['message'] = 'Success';
            $response['data'] = $card;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = '';
            return response()->json($response);
        }
    }

    public function addcard(Request $request)
    {
        $user = Auth::user();
        $userid = $user->id;
        $exists = CardDetail::where('card_number', $request->card_number)->count();
        if($exists > 0) {
            $response['status'] = "false";
            $response['message'] = "This card already added.";
            $response['data'] = '';
        } else {
            $card = CardDetail::create($request->merge(['user_id' => $userid])->all());

            if ($card) {
                $response['status'] = "true";
                $response['message'] = "Your card details saved successfully.";
                $response['data'] = $card;
            } else {
                $response['status'] = "false";
                $response['message'] = "Error while adding card.";
                $response['data'] = '';
            }
        }
        return response()->json($response);
    }

    public function updateaddcard(Request $request)
    {
        $cardID = $request->card_id;
        $card = CardDetail::find($cardID);

        $card->card_number = trim($request->card_number);
        $card->expire_date = trim($request->expire_date);
        $card->cvv = trim($request->cvv);
        $card->card_holder = trim($request->card_holder);

        try {
            $data = $card->save();
            $response['status'] = "true";
            $response['message'] = "Card details saved successfully.";
            $response['data'] = $data;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = "Error while updating card.";
            $response['data'] = '';
            return response()->json($response);
        }
    }
    public function deletecard(Request $request)
    {
        $cardid = $request->cardid;
        CardDetail::where('id', $cardid)->delete();
        $response['status'] = "true";
        return response()->json($response);
    }
}
