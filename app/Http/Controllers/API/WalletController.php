<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Stripe;

class WalletController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function mywallet()
    {
        $user = Auth::user();
        $userid = $user->id;
        $wallet = Wallet::firstWhere('user_id', $userid);

        if ($wallet != null) {
            if (env("PAYMENT_ENV") == 'prod')
                $rec = array(
                    'balance' => $wallet->balance,
                    'paystackPubKey' => env("PAYSTACK_PROD_PUBLISHABLE_KEY"),
                    'paystackSecKey' => env("PAYSTACK_PROD_SECRET_KEY"),
                );
            else
                $rec = array(
                    'balance' => $wallet->balance,
                    'paystackPubKey' => env("PAYSTACK_TEST_PUBLISHABLE_KEY"),
                    'paystackSecKey' => env("PAYSTACK_TEST_SECRET_KEY"),
                );
        } else {
            if (env("PAYMENT_ENV") == 'prod')
                $rec = array(
                    'balance' => '0.00',
                    'paystackPubKey' => env("PAYSTACK_PROD_PUBLISHABLE_KEY"),
                    'paystackSecKey' => env("PAYSTACK_PROD_SECRET_KEY"),
                );
            else
                $rec = array(
                    'balance' => '0.00',
                    'paystackPubKey' => env("PAYSTACK_TEST_PUBLISHABLE_KEY"),
                    'paystackSecKey' => env("PAYSTACK_TEST_SECRET_KEY"),
                );
            Wallet::create([
                'user_id' => $userid,
                'balance' => '0.00',
            ]);
        }
        $response['message'] = "success";
        $response['status'] = "true";
        $response['data'] = $rec;
        return response()->json($response);
    }

    public function stripepay(Request $request)
    {
        if (env("PAYMENT_ENV") == 'prod')
            Stripe\Stripe::setApiKey(env('STRIPE_PROD_SECRET_KEY'));
        else
            Stripe\Stripe::setApiKey(env('STRIPE_TEST_SECRET_KEY'));
        try {
            $paymentMethod = Stripe\PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);
            $paymentIntent = Stripe\PaymentIntent::create([
                "amount" => $request->amount * 100,
                "currency" => "NGN",
                "receipt_email" => $request->email,
                "payment_method" => $paymentMethod,
                "confirmation_method" => "automatic",
                "confirm" => true
            ]);
            if ($paymentIntent['amount_received'] != null) {
                $response['status'] = "true";
                $response['message'] = "Stripe payment successed.";
            } else {
                $response['status'] = "false";
                $response['message'] = "Stripe payment failed.";
            }
            return response()->json($response);
        } catch (Stripe\Exception\ApiErrorException $e) {
            $list = explode(") ", $e);
            if ($list != null && sizeof($list) > 0)
                $msg = $list[sizeof($list) - 1];
            $response['status'] = "false";
            $response['message'] = $msg;
            return response()->json($response);
        }
    }

    public function addmoneytowallet(Request $request)
    {
        $user = Auth::user();
        $userid = $user->id;

        if ($request->amount <= 0) {
            $response['status'] = "false";
            $response['message'] = "Invalid amount.";
            $response['data'] = '';
            return response()->json($response);
        }

        try {
            $amount = $request->amount;
            $totalamt = $request->totalamount;

            $wallet = Wallet::firstWhere('user_id', $userid);
            $wallet->balance += (double)$amount;
            $wallet->save();

            $walletDetail = array('balance' => $wallet->balance);

            ///// create a transaction for this
            $NewTransaction = new Transaction;
            $NewTransaction->user_id = $userid;
            $NewTransaction->touser_id = $userid;
            $NewTransaction->transaction_type = 'wallet';
            $NewTransaction->amount = $amount;
            $NewTransaction->status = 1;
            $NewTransaction->privacy = 'private';
            $NewTransaction->caption = 'Added to wallet';
            $NewTransaction->save();

            $response['status'] = "true";
            $response['message'] = "Money added to your wallet";
            $response['data'] = $walletDetail;

            $curb = DB::table('adminbalance')->where('id', 1)->get()->first();
            $cb = $curb->balance;
            $totalbal = $cb + $totalamt - $amount;

            DB::update('update adminbalance set balance =' . $totalbal);
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();//"System Error ";
            $response['data'] = '';
            return response()->json($response);
        }
    }
}
