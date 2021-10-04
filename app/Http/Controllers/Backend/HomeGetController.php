<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HomeGetController extends Controller
{
    public function index()
    {
        $User = User::where('user_status', 'on')->where('roll_id', 3)->get();
        $totalActiveUsers = count($User);

        $TUser = User::where('roll_id', 3)->get();
        $totalUsers = count($TUser);

        $balance = DB::table('adminbalance')->get()->first();

        return view("backend.index", compact(array('totalActiveUsers', 'totalUsers', 'balance')));
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
