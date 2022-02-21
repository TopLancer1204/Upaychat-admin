<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getJobs(Request $requesaddbankt)
    {
        // $user = Auth::user();
        // $userid = $user->id;

        $joblist = Job::select('id', 'title', 'description', 'type', 'tech', 'location', 'remote', 'post_date')->get();
        // return $joblist;
        $jlist = array();

        if (count($joblist) < 1)
            $response['message'] = 'You have not added any job details';
        else {
            foreach ($joblist as $job)
                $jlist[] =
                    array(
                        'id' => $job->id,
                        'title' => $job->title,
                        'description' => $job->description,
                        'type' => $job->type,
                        'tech' => $job->tech,
                        'location' => $job->location,
                        'remote' => $job->remote,
                        'post_date' => $job->post_date
                    );
            $response['message'] = 'Success';
        }
        $response['data'] = $jlist;
        $response['status'] = "true";
        return response()->json($response);
    }

    public function getJob(Request $request)
    {
        // $user = Auth::user();

        try {
            $job = Job::where("id", $request->job_id)->first();

            $selectedJob = array(
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'type' => $job->type,
                'tech' => $job->tech,
                'location' => $job->location,
                'remote' => $job->remote,
                'post_date' => $job->post_date
            );

            $response['status'] = "true";
            $response['message'] = 'Success';
            $response['data'] = $selectedJob;
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
