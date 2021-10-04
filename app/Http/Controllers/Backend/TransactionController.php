<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    function getallUsers()
    {
        $allUsers = User::all();
        return view('backend.transactions.userlist')->with('users', $allUsers);
    }

    public function getTransactions(Request $request)
    {
        $userdet = User::find($request->userId);

        $alltransactions = Transaction::where([['user_id', $request->userId], ['status', 1]])->orWhere([['touser_id', $request->userId], ['status', 1]])->orderBy('created_at', 'DESC')->get();

        $transactions = array();
        $publictransactions = array();
        $privatetransactions = array();

        if (count($alltransactions) > 0) {
            foreach ($alltransactions as $alltransaction) {
                $userI = $alltransaction->user_id;

                $fuser = User::where('id', $userI)->first();
                $fromuser = $fuser->firstname . " " . $fuser->lastname;

                $userT = User::where('id', $alltransaction->touser_id)->first();
                $touser = $userT->firstname . " " . $userT->lastname;

                $transactions[] = array
                (
                    'id' => $alltransaction->id,
                    'from' => $fromuser,
                    'touser' => $touser,
                    'amount' => $alltransaction->amount,
                    'privacy' => $alltransaction->privacy,
                    'date' => $alltransaction->created_at
                );

                if ($alltransaction->privacy == 'public') {
                    $publictransactions[] = array
                    (
                        'id' => $alltransaction->id,
                        'from' => $fromuser,
                        'touser' => $touser,
                        'amount' => $alltransaction->amount,
                        'privacy' => $alltransaction->privacy,
                        'date' => $alltransaction->created_at
                    );
                }
                if ($alltransaction->privacy == 'private') {
                    $privatetransactions[] = array
                    (
                        'id' => $alltransaction->id,
                        'from' => $fromuser,
                        'touser' => $touser,
                        'amount' => $alltransaction->amount,
                        'privacy' => $alltransaction->privacy,
                        'date' => $alltransaction->created_at
                    );
                }
            }
        }

        return view('backend.transactions.transactions')->with(
            [
                'settings' => $transactions,
                'public' => $publictransactions,
                'private' => $privatetransactions,
                'user' => $userdet
            ]);
    }

    function delete(Request $request)
    {
        try {
            Transaction::where('id', $request->id)->delete();
            return response(['status' => 'success', 'title' => 'Successful', 'content' => 'Transaction deleted']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Failed', 'content' => 'Transaction could not be deleted']);
        }
    }
}
