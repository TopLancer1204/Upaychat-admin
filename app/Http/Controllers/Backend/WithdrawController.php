<?php

namespace App\Http\Controllers\Backend;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\FcmJob;
use App\Jobs\SmsJob;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\BankDetail;
use App\Models\User;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function getWithdraws()
    {
        $settings = Withdrawal::all();
        $list = array();
        foreach ($settings as $therequest) {
            $uid = $therequest->user_id;
            $s = User::firstWhere('id', $uid);
            if ($s) {
                $fullname = $s->firstname . " " . $s->lastname;

                $bnk = BankDetail::firstWhere('user_id', $uid);
                $bank = array('holdername' => $bnk->account_holder_name, 'bankName' => $bnk->bank, 'accountno' => $bnk->account_no);
                $list[] = array(
                    'id' => $therequest->id,
                    'name' => $fullname,
                    'amount' => $therequest->amount,
                    'bankdetail' => $bank,
                    'status' => $therequest->status,
                    'reqDate' => $therequest->created_at
                );
            }
        }
        return view('backend.withdraws.withdraws')->with('settings', $list);
    }

    public function getSettingsEdit($settingId)
    {
        $setting = Setting::where('id', $settingId)->first();
        return view('backend.settings.setting-edit')->with('setting', $setting);
    }

    public function postWithdraws(Request $request)
    {
        if (isset($request->accept)) {
            try {
                $withdrawal = Withdrawal::where('id', $request->id)->first();
                if ($withdrawal != null && $withdrawal->status == 0) {
                    $withdrawal->status = 1;
                    $withdrawal->save();

                    $transactionRequest = Transaction::where('id', $withdrawal->trans_id)->first();
                    $transactionRequest->status = 1;
                    $transactionRequest->save();

                    $message = "Your withdraw request for ₦" . number_format($transactionRequest->amount, 2, '.', ',') . " has been accepted on UpayChat.";
                    $user = User::find($withdrawal->user_id);
                    if ($user != null) {
                        Helper::sendEmail($user->email, $message, "Withdraw request ₦" . number_format($transactionRequest->amount, 2, '.', ','));

                        SmsJob::dispatch($user->mobile, $message);

                        if ($user->fcm_token != null && $user->fcm_token != '')
                            FcmJob::dispatch($user->fcm_token, "Withdraw", $message);
                    }

                    return response(['status' => 'success', 'title' => 'Success', 'content' => 'Withdraw requests accepted']);
                } else {
                    return response(['status' => 'error', 'title' => 'Error', 'content' => 'Withdraw requests could not accepted']);
                }
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Error', 'content' => 'Withdraw requests could not accepted']);
            }
        }
    }
}
