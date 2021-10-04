<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getuser(Request $request)
    {
        $search = $request->search;

        if ($search == '') {
            $employees = User::orderby('firstname', 'asc')->select('id', 'firstname', 'lastname')->limit(10)->get();
        } else {
            $employees = User::orderby('name', 'asc')->select('id', 'firstname', 'lastname')->where('firstname', 'like', '%' . $search . '%')->orWhere('lastname', 'like', '%' . $search . '%')->limit(10)->get();
        }

        $response = array();
        foreach ($employees as $employee) {
            $response[] = array(
                "id" => $employee->id,
                "text" => $employee->firstname . " " . $employee->lastname
            );
        }

        echo json_encode($response);
        exit;
    }

    public function index()
    {
        return view('backend.reports.transactions');
    }

    public function searchreport(Request $request)
    {
        //print_r($request->all())	;die;

        $reportTye = $request->reportType;
        $duration = $request->duration;

        if ($duration == "Monthly") {
            $startDt = date("Y") . "-" . date("m") . "-1";
            $endDt = date("Y") . "-" . date("m") . "-30";
        }
        if ($duration == "custom") {
            $startDt = $request->startdt;
            $endDt = $request->enddt;
        }

        if ($reportTye == "WholeTransaction") {
            $results = DB::select(DB::raw("SELECT SUM(`amount`) as total,user_id from transactions where `transaction_type` in('pay','request') and created_at between '$startDt' and '$endDt' group BY user_id"));

            $arr[] = array('User name', 'Mobile no', 'Total Transaction amount');
            if (count($results) > 0) {
                foreach ($results as $res) {
                    $userid = $res->user_id;
                    $u = User::firstWhere('id', $userid);
                    $uname = $u->firstname . " " . $u->lastname;
                    $mobile = $u->mobile;
                    $total = '$' . $res->total;
                    $arr[] = array($uname, $mobile, $total);
                }
            }
            $fileName = 'WholeTransaction.csv';
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$fileName}");
            header("Expires: 0");
            header("Pragma: public");
            # add headers for each column in the CSV download
            //array_unshift($arr, array_keys($arr[0]));
            $FH = fopen('php://output', 'w');
            foreach ($arr as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);

            exit;
            //		return response()->stream($callback, 200, $headers);
            //return response(['status' => 'success', 'title' => 'Success', 'content' => "Downloading"]);
        }

        if ($reportTye == "UserTransaction") {
            $userid = $request->selUser;
            $results = DB::select(DB::raw("SELECT user_id,touser_id,transaction_type,amount,privacy,created_at from transactions where `transaction_type` in('pay','request') and user_id =$userid and  created_at between '$startDt' and '$endDt'"));

            $arr[] = array('Date', 'The money transact to', 'Transaction Amount', 'Privacy');
            if (count($results) > 0) {
                foreach ($results as $res) {
                    $userid = $res->touser_id;
                    $u = User::firstWhere('id', $userid);
                    $uname = $u->firstname . " " . $u->lastname;
                    $date = date("Y-m-d", $res->created_at);
                    $amount = "" . $res->amount;
                    $privacy = $res->privacy;
                    $arr[] = array($date, $uname, $amount, $privacy);
                }
            }
            $fileName = 'UserTransaction.csv';
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Description: File Transfer');
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename={$fileName}");
            header("Expires: 0");
            header("Pragma: public");
            # add headers for each column in the CSV download
            //array_unshift($arr, array_keys($arr[0]));
            $FH = fopen('php://output', 'w');
            foreach ($arr as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);

            exit;
        }
    }
}
