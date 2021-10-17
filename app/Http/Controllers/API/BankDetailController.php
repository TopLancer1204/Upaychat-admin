<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BankDetail;
use Illuminate\Support\Facades\Auth;

class BankDetailController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function bankdetails(Request $requesaddbankt)
    {
        $user = Auth::user();
        $userid = $user->id;

        $banklist = BankDetail::where('user_id', $userid)->orderby('updated_at', 'desc')->get();
        $blist = array();

        if (count($banklist) < 1)
            $response['message'] = 'You have not added any bank details';
        else {
            foreach ($banklist as $bank)
                $blist[] =
                    array(
                        'id' => $bank->id,
                        'bank_name' => $bank->bank,
                        'account_holder_name' => $bank->account_holder_name,
                        'account_no' => $bank->account_no
                    );
            $response['message'] = 'Success';
        }
        $response['data'] = $blist;
        $response['status'] = "true";
        return response()->json($response);
    }

    public function getbank(Request $request)
    {
        $user = Auth::user();

        try {
            $ban = BankDetail::where("id", $request->bank_id)->first();

            $bank = array
            (
                'id' => $ban->id,
                'bank_name' => $ban->bank,
                'account_holder_name' => $ban->account_holder_name,
                'account_no' => $ban->account_no
            );

            $response['status'] = "true";
            $response['message'] = 'Success';
            $response['data'] = $bank;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = '';
            return response()->json($response);
        }
    }

    public function addbank(Request $request)
    {
        $user = Auth::user();
        $userid = $user->id;

        $bank = new BankDetail;

        $bank->user_id = $userid;
        $bank->bank = trim($request->bank_name);
        $bank->account_no = trim($request->account_no);
        $bank->account_holder_name = trim($request->account_holder_name);
        //$bank->branch_city  = trim($request->branch_city);
        //$bank->ifsc  =    trim($request->ifsc_code);

        if ($bank->save()) {
            $response['status'] = "true";
            $response['message'] = "Your bank details saved successfully.";
            $response['data'] = $bank;
        } else {
            $response['status'] = "false";
            $response['message'] = "Error while adding bank.";
            $response['data'] = '';
        }
        return response()->json($response);
    }

    public function updateaddbank(Request $request)
    {
        $bankID = $request->bank_id;
        $bank = BankDetail::find($bankID);

        $bank->bank = trim($request->bank_name);
        $bank->account_no = trim($request->account_no);
        $bank->account_holder_name = trim($request->account_holder_name);
        //$bank->branch_city  = trim($request->branch_city);

        try {
            $bank->save();
            $data = array('bank_name' => $request->bank_name, 'account_no' => $request->account_no, 'account_holder_name' => $request->account_holder_name);
            $response['status'] = "true";
            $response['message'] = "Bank details saved successfully.";
            $response['data'] = $data;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = "Error while updating bank.";
            $response['data'] = '';
            return response()->json($response);
        }
    }
    public function deletebank(Request $request)
    {
        $bankid = $request->bankid;
        BankDetail::where('id', $bankid)->delete();
        $response['status'] = "true";
        return response()->json($response);
    }
}
