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
use App\Models\Wallet;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function getWithdraws()
    {
        $settings = Withdrawal::orderby('id', 'desc')->get();
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
            $_accept = $request->accept == "true" ? "accepted" : "rejected";
            try {
                $withdrawal = Withdrawal::where('id', $request->id)->first();
                if ($withdrawal != null && $withdrawal->status == 0) {
                    $transactionRequest = Transaction::where('id', $withdrawal->trans_id)->first();
                    $withdrawal->status = 1;
                    $transactionRequest->status = 1;
                    if($_accept == "rejected") {
                        $withdrawal->status = 2;
                        $transactionRequest->status = 2;

                        $balance = Wallet::where('user_id', $withdrawal->user_id)->value('balance') + $withdrawal->amount;
                        Wallet::where('user_id', $withdrawal->user_id)->update(['balance' => $balance]);
                    }
                    $withdrawal->save();
                    $transactionRequest->save();
                    $message = "Your withdraw request for ₦" . number_format($transactionRequest->amount, 2, '.', ',') . " has been ".$_accept." on UpayChat.";

                    $user = User::find($withdrawal->user_id);
                    if ($user != null) {
                        Helper::sendEmail($user->email, $message, "Withdraw request ₦" . number_format($transactionRequest->amount, 2, '.', ','));

                        SmsJob::dispatch($user->mobile, $message);

                        if ($user->fcm_token != null && $user->fcm_token != '')
                            FcmJob::dispatch($user->fcm_token, "Withdraw", $message);
                    }

                    return response(['status' => 'success', 'title' => 'Success', 'content' => 'Withdraw requests '.$_accept]);
                } else {
                    return response(['status' => 'error', 'title' => 'Error', 'content' => 'Withdraw requests could not '.$_accept]);
                }
            } catch (\Exception $e) {
                dd($e);
                return response(['status' => 'error', 'title' => 'Error', 'content' => 'Withdraw requests could not '.$_accept]);
            }
        }
    }
}
