<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function mycomplains(Request $request)
    {
        $user = Auth::user();
        $userid = $user->id;
        $Complaint = Complaint::where('user_id', '=', $userid)->get();

        if (!count($Complaint)) {
            $response['message'] = 'No complains';
            $response['data'] = [];
        } else {
            $complist = array();

            foreach ($Complaint as $comp) {
                $complist[] = array(
                    'subject' => $comp->subject,
                    'message' => $comp->message,
                    'reply' => $comp->reply,
                    'status' => $comp->status,
                    'created_at' => $comp->created_at
                );
            }
            $response['message'] = 'Total ' . count($complist) . ' Complains found';
            $response['data'] = $complist;
        }
        $response['status'] = "true";
        return response()->json($response);
    }
}
