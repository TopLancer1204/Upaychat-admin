<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function getFaqs(Request $requesaddbankt)
    {
        // $user = Auth::user();
        // $userid = $user->id;

        $faqlist = Faq::select('id', 'question', 'answer')->get();
        // return $joblist;
        $flist = array();

        if (count($faqlist) < 1)
            $response['message'] = 'You have not added any job details';
        else {
            foreach ($faqlist as $faq)
                $flist[] =
                    array(
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                    );
            $response['message'] = 'Success';
        }
        $response['data'] = $flist;
        $response['status'] = "true";
        return response()->json($response);
    }
}
