<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Jobs\FcmJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\PostLike;
use App\Models\BankDetail;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\PostComment;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;

// transaction status == 0 waiting approval
// status == 1 completed
// status == 2 cancelled/deny
// status == 3 a payment made to a person who have no wallet or un installed APP not sure how to detect it. may be un installation time we do it.
// status == 4 transaction to unregistered user

class TransactionController extends Controller
{
    public function pendingrequest()
    {
        $user = Auth::user();
        $userid = $user->id;

        $transactions = Transaction::where([['touser_id', $userid], ['status', 0], ['transaction_type', 'withdrawal']])
            ->orWhere([['touser_id', $userid], ['status', 0], ['transaction_type', 'request']])
            ->orWhere([['user_id', $userid], ['status', 0], ['transaction_type', 'request']])
            ->orWhere([['user_id', $userid], ['status', 4]])
            ->orderBy('created_at', 'DESC')->get();

        $trlist = array();

        foreach ($transactions as $tran) {
            $requester = User::find($tran->user_id);
            if ($requester == null) continue;
            $touserImage = '';
            if ($tran->transaction_type == 'withdrawal') {
                $message = "You requested withdrawal";
                $touserImage = $requester->avatar;
            } else if ($tran->user_id == $userid) {
                if ($tran->status == 4) {
                    if ($tran->transaction_type == 'pay')
                        $message = "You paid to " . $tran->touser_id;
                    else
                        $message = "You requested from " . $tran->touser_id;
                } else {
                    $receipter = User::find($tran->touser_id);
                    $message = "You requested from " . $receipter->firstname . " " . $receipter->lastname;
                    $touserImage = $receipter->avatar;
                }
            } else {
                $message = $requester->firstname . " " . $requester->lastname . " requested from you";
                $touserImage = $requester->avatar;
            }

            $trlist[] = array(
                'id' => $tran->id,
                'amount' => $tran->amount,
                'timestamp' => $tran->created_at,
                'fromuser_id' => $tran->user_id,
                'to_userimage' => $touserImage,
                'message' => $message,
                'caption' => $tran->caption,
                'privacy' => $tran->privacy,
                'status' => $tran->status
            );
        }

        $response['status'] = "true";
        $response['message'] = 'success';
        $response['data'] = $trlist;
        return response()->json($response);
    }

    public function mytransactionshistory()
    {
        $user = Auth::user();
        $userid = $user->id;

        $transactions = Transaction::where([['user_id', $userid], ['status', 1]])->orWhere([['touser_id', $userid], ['status', 1]])->orderBy('created_at', 'DESC')->get();

        $trlist = array();
        foreach ($transactions as $tran) {
            $trans_user;
            switch ($tran->transaction_type) {
                case 'pay':
                case 'request':
                    if ($tran->user_id == $userid) $trans_user = User::find($tran->touser_id);
                    else $trans_user = User::find($tran->user_id);
                    break;
                case "withdrawal":
                case "wallet":
                case "takeback":
                    $trans_user = User::find($tran->user_id);
                    break;
            }

            $trlist[] = array(
                'tran' => $tran,
                'user' => $trans_user
            );
        }
        $response['status'] = "true";
        $response['message'] = 'success';
        $response['data'] = $trlist;
        return response()->json($response);
    }

    public function transactionshistory()
    {
        $user = Auth::user();
        $userid = $user->id;

        $publicTransactions = Transaction::where([['privacy', 'public'], ['status', 1]]);

        $transactions = Transaction::where([['user_id', $userid], ['privacy', 'private'], ['status', 1]])
            ->orWhere([['touser_id', $userid], ['privacy', 'private'], ['status', 1]])
            ->union($publicTransactions)
            ->orderBy('created_at', 'DESC')
            ->get();

        $trlist = array();
        foreach ($transactions as $tran) {
            $message = '';
            $touserImage = '';

            // get comments on this post
            $cmt = PostComment::where('tran_id', $tran->id)
                ->orderBy('created_at', 'DESC')
                ->get();
            $comments = array();
            foreach ($cmt as $com) {
                $comuser = User::find($com->user_id);
                $comments[] = array(
                    'user_id' => $com->user_id,
                    'username' => $comuser == null ? "" : $comuser->username,
                    'comment' => $com->comment,
                    'timestamp' => $com->created_at,
                );
            }

            // check if this user liked this post or not
            $like = 0;
            $lk = PostLike::where('post_id', $tran->id)->where('user_id', $userid)->get();
            if (count($lk)) {
                $like = 1;
            }
            $_empty = (Object)['firstname' => "@", "lastname" => "#", "avatar" => ""];

            $username = '';
            switch ($tran->transaction_type) {
                case 'pay':
                    if ($tran->user_id == $userid) {
                        $receiver = User::find($tran->touser_id) ?? $_empty;
                        $message = "You paid ₦" . number_format($tran->amount, 2, '.', ',') . " to " . $receiver->firstname . " " . $receiver->lastname;
                        $username = $receiver->firstname . " " . $receiver->lastname;
                        $touserImage = $receiver->avatar;
                    } elseif ($tran->touser_id == $userid) {
                        $donnar = User::find($tran->user_id) ?? $_empty;
                        $message = $donnar->firstname . " " . $donnar->lastname . " paid you ₦" . number_format($tran->amount, 2, '.', ',');
                        $username = $donnar->firstname . " " . $donnar->lastname;
                        $touserImage = $donnar->avatar;
                    } else {
                        $donnar = User::find($tran->user_id) ?? $_empty;
                        $receiver = User::find($tran->touser_id) ?? $_empty;
                        $message = $donnar->firstname . " " . $donnar->lastname . " paid " . $receiver->firstname . " " . $receiver->lastname . " ₦" . number_format($tran->amount, 2, '.', ',');
                        $touserImage = $donnar->avatar;
                    }
                    break;

                case 'request':
                    if ($tran->user_id == $userid) {
                        $receiver = User::find($tran->touser_id) ?? $_empty;
                        $message = "You requested ₦" . number_format($tran->amount, 2, '.', ',') . " from " . $receiver->firstname . " " . $receiver->lastname;
                        $touserImage = $receiver->avatar;
                    } elseif ($tran->touser_id == $userid) {
                        $donnar = User::find($tran->user_id) ?? $_empty;
                        $message = $donnar->firstname . " " . $donnar->lastname . " requested ₦" . number_format($tran->amount, 2, '.', ',') . " from you";
                        $touserImage = $donnar->avatar;
                    } else {
                        $donnar = User::find($tran->user_id) ?? $_empty;
                        $receiver = User::find($tran->touser_id) ?? $_empty;
                        $message = $donnar->firstname . " " . $donnar->lastname . " requested ₦" . number_format($tran->amount, 2, '.', ',') . " from " . $receiver->firstname . " " . $receiver->lastname;
                        $touserImage = $donnar->avatar;
                    }
                    break;

                case "withdrawal":
                    $message = "You transferred ₦" . number_format($tran->amount, 2, '.', ',') . " to your bank";
                    $donnar = User::find($tran->user_id) ?? $_empty;
                    $username = "Withdrawal";
                    $touserImage = $donnar->avatar;
                    break;

                case "wallet":
                    $message = "You added ₦" . number_format($tran->amount, 2, '.', ',') . " to your wallet";
                    $donnar = User::find($tran->user_id) ?? $_empty;
                    $username = "Deposit";
                    $touserImage = $donnar->avatar;
                    break;

                case "takeback":
                    $donnar = User::find($tran->user_id) ?? $_empty;
                    $message = "You take back ₦" . number_format($tran->amount, 2, '.', ',') . " from " . $donnar->firstname . " " . $donnar->lastname;
                    $touserImage = $donnar->avatar;
                    break;
            }

            $trlist[] = array(
                'id' => $tran->id,
                'from_id' => $tran->user_id,
                'username' => $username,
                'amount' => $tran->amount,
                'timestamp' => $tran->created_at,
                'to_userimage' => $touserImage,
                'message' => $message,
                'caption' => $tran->caption,
                'comments' => $comments,
                'like' => $like,
                'likecount' => $tran->likes,
                'privacy' => $tran->privacy,
                'mine' => $tran->user_id == $userid || $tran->touser_id == $userid,
            );
        }
        $response['status'] = "true";
        $response['message'] = 'success';
        $response['data'] = $trlist;
        return response()->json($response);
    }

    public function addtransaction(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader != 'application/json') {
            $response['status'] = "false";
            $response['message'] = "Not a valid API request.";
            $response['data'] = [];
            return response()->json($response, 400);
        }
        if (!($request->caption) || !($request->amount) || !($request->transaction_type) || !(trim($request->privacy)) || !($request->touser_id)) {
            $response['status'] = "false";
            $response['message'] = "Missing params.";
            $response['data'] = [];
            return response()->json($response);
        }
        if ($request->amount <= 0) {
            $response['status'] = "false";
            $response['message'] = "Invalid amount.";
            $response['data'] = [];
            return response()->json($response);
        }

        $user = Auth::user();
        $userid = $user->id;

        if ($request->touser_id == "-1") {
            $pending = new Transaction;
            $pending->user_id = $userid;
            $pending->touser_id = trim($request->user);
            $pending->transaction_type = trim($request->transaction_type);
            $pending->amount = trim($request->amount);
            $pending->caption = trim($request->caption);
            $pending->privacy = trim($request->privacy);
            $pending->status = 4;
            $pending->save();

            if ($request->transaction_type == 'pay') {
                // update this users wallet
                $payerwalet = Wallet::firstWhere('user_id', $userid);
                $curBal = $payerwalet->balance;
                if ($curBal < $request->amount) {
                    $response['status'] = "false";
                    $response['message'] = "Insufficient balance in wallet";
                    $response['data'] = [];
                    return response()->json($response);
                }
                $newBalance = $curBal - $request->amount;
                $payerwalet->balance = $newBalance;
                $payerwalet->save();

                $subject = $user->firstname . " " . $user->lastname . " paid ₦" . number_format($request->amount, 2, '.', ',');
                $message = $user->firstname . " " . $user->lastname . " paid you ₦" . number_format($request->amount, 2, '.', ',') . " on UpayChat. Go to upaychat.com to Download App and pick up money.";
            } else {
                $subject = $user->firstname . " " . $user->lastname . " requested ₦" . number_format($request->amount, 2, '.', ',');
                $message = $user->firstname . " " . $user->lastname . " requested ₦" . number_format($request->amount, 2, '.', ',') . " from you on UpayChat. Go to upaychat.com to Download App and see request.";
            }
            $result = filter_var($request->user, FILTER_VALIDATE_EMAIL);
            if ($result != false)
                Helper::sendEmail($request->user, $message, $subject);
            else
                Helper::sendSMS($request->user, $message);

            $response['status'] = "true";
            $response['message'] = "Transaction success.";
            $response['data'] = $pending;
            return response()->json($response);
        }

        $transaction = new Transaction;
        $transaction->user_id = $userid;
        $transaction->touser_id = trim($request->touser_id);
        $transaction->transaction_type = trim($request->transaction_type);
        $transaction->amount = trim($request->amount);
        $transaction->caption = trim($request->caption);
        $transaction->privacy = trim($request->privacy);

        try {
            if (strtolower($request->transaction_type) == 'pay') {
                $transaction->status = 1;

                // update this users wallet
                $payerwalet = Wallet::firstWhere('user_id', $userid);
                $curBal = $payerwalet->balance;
                if ($curBal < $request->amount) {
                    $response['status'] = "false";
                    $response['message'] = "Insufficient balance in wallet";
                    $response['data'] = $transaction;
                    return response()->json($response);
                }
                $newBalance = $curBal - $request->amount;
                $payerwalet->balance = $newBalance;
                $payerwalet->save();

                /// update receiver wallet.
                $receiverwalet = Wallet::firstWhere('user_id', $request->touser_id);
                $curBal = $receiverwalet->balance;
                $newBalance = $curBal + $request->amount;
                $receiverwalet->balance = $newBalance;
                $receiverwalet->save();

                if ($receiverwalet->status == 0)
                    $transaction->status = 3;
                $subject = $user->firstname . " " . $user->lastname . " paid ₦" . number_format($request->amount, 2, '.', ',');
                $message = $user->firstname . " " . $user->lastname . " paid you ₦" . number_format($request->amount, 2, '.', ',') . "\nAvailable Balance: ₦" . number_format($receiverwalet->balance, 2, '.', ',');
            } elseif (strtolower($request->transaction_type) == 'request') {
                $transaction->status = 0;
                $subject = $user->firstname . " " . $user->lastname . " requested ₦" . number_format($request->amount, 2, '.', ',');
                $message = $user->firstname . " " . $user->lastname . " requested ₦" . number_format($request->amount, 2, '.', ',') . " from you on Upaychat";
            }
            $transaction->save();

            ////////////////////// notification for receiver ///////////////
            $Noti = new Notification;
            $Noti->post_id = $transaction->id;
            $Noti->user_id = $transaction->touser_id;
            $Noti->notification = $message;
            $Noti->save();

            $message = $message . " on UpayChat.";

            $receiver = User::find($transaction->touser_id);
            if ($receiver != null) {
                Helper::sendEmail($receiver->email, $message, $subject);

                Helper::sendSMS($receiver->mobile ?? $receiver->id ?? 0, $message);

                if ($receiver->fcm_token != null && $receiver->fcm_token != '')
                    FcmJob::dispatch($receiver->fcm_token, $user->username, $message);
            }

            $response['status'] = "true";
            $response['message'] = "Transaction success.";
            $response['data'] = $transaction;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = [];
            return response()->json($response);
        }
    }

    public function acceptrequest(Request $request)
    {
        if (!$request->transactionId || !$request->privacy) {
            $response['status'] = "false";
            $response['message'] = "Missing parameter in request";
            $response['data'] = [];
            return response()->json($response);
        }
        $transactionID = $request->transactionId;
        $user = Auth::user();
        $userid = $user->id;

        try {
            $transactionRequest = Transaction::find($transactionID);
            $amount = $transactionRequest->amount;
            $paytoUser = $transactionRequest->user_id;

            // need to check if this logged user who is accepting, have sufficient balance in his wallet to accept request
            $payerswallet = Wallet::firstWhere('user_id', $userid);

            if ($payerswallet->balance >= $amount) {  // he have enough balance so lets transact
                $curBal = $payerswallet->balance;
                $newBalance = $curBal - $amount;
                $payerswallet->balance = $newBalance;
                $payerswallet->save();

                // increase same amount in receiver
                $receiverwallet = Wallet::firstWhere('user_id', $paytoUser);
                $curBal = $receiverwallet->balance;
                $newBalance = $curBal + $amount;
                $receiverwallet->balance = $newBalance;
                $receiverwallet->save();

                // change the status of this request to completed.
                $transactionRequest->privacy = $request->privacy;
                $transactionRequest->status = 1;
                $transactionRequest->save();

                ////////////////////// notification for sender ///////////////
                $message = $user->firstname . " " . $user->lastname . " accepted your request for ₦" . number_format($amount, 2, '.', ',');
                $Noti = new Notification;
                $Noti->post_id = $transactionID;
                $Noti->user_id = $paytoUser;
                $Noti->notification = $message;
                $Noti->save();

                $subject = $user->firstname . " " . $user->lastname . " accepted your request for ₦" . number_format($amount, 2, '.', ',');
                $message = $message . " on UpayChat.";

                $receiver = User::find($paytoUser);
                if ($receiver != null) {
                    Helper::sendEmail($receiver->email, $message, $subject);

                    Helper::sendSMS($receiver->mobile ?? $receiver->id ?? 0, $message);

                    if ($receiver->fcm_token != null && $receiver->fcm_token != '')
                        FcmJob::dispatch($receiver->fcm_token, $user->username, $message);
                }

                $data = array('balance' => $payerswallet->balance);

                $response['status'] = "true";
                $response['message'] = "Transaction successful";
                $response['data'] = $data;
            } else {
                $response['status'] = "false";
                $response['message'] = "Insufficient balance in wallet";
                $response['data'] = [];
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = [];
            return response()->json($response);
        }
    }

    public function cancelrequest(Request $request)
    {
        if ((!$request->requestId)) {
            $response['status'] = "false";
            $response['message'] = "Missing parameter in request";
            $response['data'] = [];
            return response()->json($response);
        }
        $user = Auth::user();
        $userid = $user->id;

        $transactionRequest = Transaction::find($request->requestId);

        if ($transactionRequest->status != 0 && $transactionRequest->status != 4) {
            $response['status'] = "false";
            $response['message'] = $transactionRequest->status == 1 ? "This transaction already completed" : "This transaction already canceled";
            $response['data'] = [];
            return response()->json($response);
        }

        $transactionRequest->status = 2;
        $transactionRequest->save();

        $mywallet = Wallet::firstWhere('user_id', $userid);
        if ($userid != $transactionRequest->user_id) {
            ////////////////////// notification for sender ///////////////
            $subject = $user->firstname . " " . $user->lastname . " declined your request";
            $message = $user->firstname . " " . $user->lastname . " declined your request for ₦" . number_format($transactionRequest->amount, 2, '.', ',');
            $Noti = new Notification;
            $Noti->post_id = $transactionRequest->id;
            $Noti->user_id = $transactionRequest->user_id;
            $Noti->notification = $message;
            $Noti->save();

            $message = $message . " on UpayChat.";

            $receiver = User::find($transactionRequest->user_id);
            if ($receiver != null) {
                Helper::sendEmail($receiver->email, $message, $subject);

                Helper::sendSMS($receiver->mobile ?? $receiver->id ?? 0, $message);

                if ($receiver->fcm_token != null && $receiver->fcm_token != '')
                    FcmJob::dispatch($receiver->fcm_token, $user->username, $message);
            }
        } else if ($transactionRequest->transaction_type == 'withdrawal') {
            /// update sender wallet.
            $curBal = $mywallet->balance;
            $newBalance = $curBal + $transactionRequest->amount;
            $mywallet->balance = $newBalance;
            $mywallet->save();

            $withdrawal = Withdrawal::where('trans_id', $transactionRequest->id)->first();
            $withdrawal->status = 2;
            $withdrawal->save();
        } else if ($transactionRequest->transaction_type == 'pay') {
            /// update sender wallet.
            $curBal = $mywallet->balance;
            $newBalance = $curBal + $transactionRequest->amount;
            $mywallet->balance = $newBalance;
            $mywallet->save();

            $subject = $user->firstname . " " . $user->lastname . " cancelled your payment";
            $message = $user->firstname . " " . $user->lastname . " cancelled the ₦" . number_format($transactionRequest->amount, 2, '.', ',') . " sent on " . now()->format('d/m/Y') . " on UpayChat.";
            $result = filter_var($transactionRequest->touser_id, FILTER_VALIDATE_EMAIL);
            if ($result != false) {
                Helper::sendEmail($transactionRequest->touser_id, $message, $subject);
            } else {
                Helper::sendSMS($transactionRequest->touser_id, $message);
            }
        } else if ($transactionRequest->transaction_type == 'request') {
            $subject = "You cancelled the request";
            $message = "You cancelled the ₦" . number_format($transactionRequest->amount, 2, '.', ',') . " sent on " . now()->format('d/m/Y') . " on UpayChat.";
            if($user->email){
                Helper::sendEmail($user->email, $message, $subject);
            } else {
                Helper::sendSMS($user->phone, $message);
            }
        }

        $data = array('balance' => $mywallet->balance);

        $response['status'] = "true";
        $response['message'] = "Request cancelled successfully.";
        $response['data'] = $data;
        return response()->json($response);
    }

    public function takeBack(Request $request)
    {
        if ((!$request->requestId)) {
            $response['status'] = "false";
            $response['message'] = "Missing parameter in request";
            $response['data'] = '';
            return response()->json($response);
        }
        $user = Auth::user();
        $userid = $user->id;

        try {
            $transactionRequest = Transaction::find($request->requestId);
            $amount = $transactionRequest->amount;

            $payerswallet = Wallet::firstWhere('user_id', $userid);

            $curBal = $payerswallet->balance;
            $newBalance = $curBal + $amount;
            $payerswallet->balance = $newBalance;
            $payerswallet->save();

            /// update - takeback from banned / uninstalled Wallet
            $receiveruser = $transactionRequest->touser_id;
            $receiverwallet = Wallet::firstWhere('user_id', $receiveruser);

            $RcurBal = $receiverwallet->balance;
            $RnewBalance = $RcurBal + $amount;
            $receiverwallet->balance = $RnewBalance;
            $receiverwallet->save();

            // create transaction get back
            $NewTransaction = new Transaction;
            $NewTransaction->user_id = $transactionRequest->touser_id;
            $NewTransaction->touser_id = $transactionRequest->user_id;
            $NewTransaction->transaction_type = 'takeback';
            $NewTransaction->amount = $transactionRequest->amount;
            $NewTransaction->status = 1;
            $NewTransaction->privacy = $transactionRequest->privacy;
            $NewTransaction->caption = $transactionRequest->caption;
            $NewTransaction->save();

            $response['status'] = "true";
            $response['message'] = "Take back request completed successfully";
            $response['data'] = '';
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = '';
            return response()->json($response);
        }
    }

    public function addlike(Request $request)
    {
        $user = Auth::user();
        $userid = $user->id;

        $postId = $request->post_id;

        $al = PostLike::where('user_id', $userid)->where('post_id', $postId)->get();
        if (count($al) < 1) {
            $pl = new PostLike;
            $pl->user_id = $userid;
            $pl->post_id = $postId;
            $pl->save();
            $response['message'] = "like";
        } else {
            $al->first()->delete();
            $response['message'] = "dislike";
        }

        $likes = PostLike::where('post_id', $postId)->count();

        Transaction::where('id', $postId)->update([
            'likes' => $likes,
        ]);
        $response['status'] = "true";
        $response['data'] = $likes;

        return response()->json($response);
    }

    public function addcomment(Request $request)
    {
        $user = Auth::user();
        $userid = $user->id;

        if ((!$request->transactionid) || (!$request->comment)) {
            $response['status'] = "false";
            $response['message'] = "Missing parameter in request";
            $response['data'] = [];
            return response()->json($response);
        }
        try {
            $comment = new  PostComment;
            $comment->tran_id = $request->transactionid;
            $comment->user_id = $userid;
            $comment->comment = $request->comment;
            $comment->save();

            // get comments on this post
            $cmt = PostComment::where('tran_id', $request->transactionid)
                ->orderBy('created_at', 'DESC')
                ->get();
            $comments = array();
            foreach ($cmt as $com) {
                $comuser = User::find($com->user_id);
                $comments[] = array(
                    'user_id' => $com->user_id,
                    'username' => $comuser == null ? "" : $comuser->username,
                    'comment' => $com->comment,
                    'timestamp' => $com->created_at,
                );
            }

            $response['status'] = "true";
            $response['message'] = "Comment posted successfully";
            $response['data'] = $comments;
            return response()->json($response);

        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = [];
            return response()->json($response);
        }
    }

    function addwithdrawrequest(Request $request)
    {
        if ((!$request->amount)) {
            $response['status'] = "false";
            $response['message'] = "Missing parameter in request";
            $response['data'] = '';
            return response()->json($response);
        }
        if ($request->amount <= 0) {
            $response['status'] = "false";
            $response['message'] = "Invalid amount.";
            $response['data'] = '';
            return response()->json($response);
        }
        $user = Auth::user();
        $userid = $user->id;

        $bank = BankDetail::where('user_id', $userid)->get();

        if (count($bank) < 1) {
            $response['status'] = "false";
            $response['message'] = 'Please add bank to withdraw amount';
            $response['data'] = [];
            return response()->json($response);
        }
        try {
            $user = Auth::user();
            $userid = $user->id;
            $amount = trim($request->amount);

            $transaction = new Transaction;
            $transaction->user_id = $userid;
            $transaction->touser_id = $userid;
            $transaction->transaction_type = 'withdrawal';
            $transaction->amount = $amount;
            $transaction->privacy = 'private';
            $transaction->caption = 'Withdrawal to bank';
            $transaction->status = 0;
            $transaction->save();

            $withdraw = new Withdrawal;
            $withdraw->trans_id = $transaction->id;
            $withdraw->user_id = $userid;
            $withdraw->amount = $amount;
            $withdraw->save();

            // increase same amount in receiver
            $receiverwallet = Wallet::firstWhere('user_id', $userid);
            $curBal = $receiverwallet->balance;
            $newBalance = $curBal - $amount;
            $receiverwallet->balance = $newBalance;
            $receiverwallet->save();

            $subject = $user->firstname . " " . $user->lastname . " requested to withdraw NGN" . number_format($amount, 2, '.', ',');
            $message = $user->firstname . " " . $user->lastname . "(" . $user->username . ", " . $user->mobile . ", " . $user->email . ") requested to withdraw ₦" . number_format($amount, 2, '.', ',') . " at " . now()->format('d/m/Y') . ".";
            Helper::sendEmail("support@upaychat.com", $message, $subject);
            $subject1 = "You requested to withdraw NGN" . number_format($amount, 2, '.', ',');
            $message1 = "You requested to withdraw ₦" . number_format($amount, 2, '.', ',') . " at " . now()->format('d/m/Y') . ".";
            Helper::sendEmail($user->email, $message1, $subject1);

            $data = array('balance' => $newBalance);
            $response['status'] = "true";
            $response['message'] = "Request sent successfully";
            $response['data'] = $data;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = '';
            return response()->json($response);
        }
    }
}
