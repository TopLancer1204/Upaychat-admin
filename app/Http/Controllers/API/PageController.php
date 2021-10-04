<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function faq(Request $request)
    {
        $result = Faq::select('id', 'question', 'answer')->get();
        $data = array();

        if (count($result)) {
            foreach ($result as $re)
                $data[] = array('id' => $re->id, 'question' => $re->question, 'answer' => $re->answer);

            $response['status'] = "true";
            $response['message'] = "success";
        } else {
            $response['status'] = "false";
            $response['message'] = "Something went wrong. Please Try again.";
        }
        $response['data'] = $data;
        return response()->json($response);
    }
}
