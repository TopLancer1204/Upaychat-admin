<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\IDVerification;
use App\Models\IDVerificationMeta;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\PendingSms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all();
        return view('backend.users.users')->with('users', $users);
    }

    public function getUsersAdd()
    {
        $roles = Role::all();
        return view('backend.users.user-add')->with('roles', $roles);
    }

    public function getUsersEdit($userId)
    {
        $roles = Role::all();
        $user = User::where('id', $userId)->first();
        return view('backend.users.user-edit')->with('roles', $roles)->with('user', $user);
    }

    public function postUsers(Request $request)
    {
        if (isset($request->delete)) {
            try {
                if($request->id == 0) {
                    $users = User::where('id', '!=', 1)->get();
                    foreach ($users as $key => $value) {
                        File::delete(public_path($value->avatar));
                    }
                    User::where('id', '!=', 1)->delete();
                    Withdrawal::where('id', '>', 0)->delete();
                    Wallet::where('user_id', '!=', 1)->delete();
                    Transaction::where('id', '>', 0)->delete();
                    PendingSms::where('id', '>', 0)->delete();
                } else {
                    $users = User::where('id', $request->id)->first();
                    File::delete(public_path($users->avatar));
                    User::where('id', $request->id)->delete();                    
                }
                return response(['status' => 'success', 'title' => 'successful', 'content' => 'User Deleted']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Başarısız', 'content' => 'User Could Not Delete']);
            }
        } elseif (isset($request->user_status)) {
            try {
                if($request->user_status == "lock"){
                    User::where('id', $request->id)->update(['locked' => 5]);
                } else if($request->user_status == "unlock"){
                    User::where('id', $request->id)->update(['locked' => 0]);
                } else {
                    User::where('id', $request->id)->update($request->all());
                    if($request->user_status == "off") {
                        IDVerification::where('user_id', $request->id)->metadata()->delete();
                    }
                }
                return response(['status' => 'success', 'title' => 'successful', 'content' => 'User Status Changed']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Failed!', 'content' => 'User Status Could Not Change']);
            }
        }
    }

    public function postUsersAdd(Request $request)
    {
        try {
            $date = Str::slug(Carbon::now());
            $imageName = Str::slug($request->firstname) . '-' . $date;
            Image::make($request->file('image'))->save(public_path('/uploads/users/') . $imageName . '.jpg')->encode('jpg', '50');

            $fullname = $request->firstname . " " . $request->lastname;
            User::create([
                'avatar' => '/uploads/users/' . $imageName . '.jpg',
                'name' => $fullname,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'roll_id' => $request->roll_id,
                'roll' => 'customer',
                'user_status' => $request->user_status == 'on' ? 'on' : 'off',
            ]);
            return response(['status' => 'success', 'title' => 'Success !', 'content' => 'User Added']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Failled !', 'content' => $e->getMessage() . " " . 'Couldn\'t Add User']);
        }
    }

    public function postUsersEdit(Request $request, $userId)
    {
        try {
            $users = User::where('id', $userId)->first();
            if ($request->hasFile('avatar')) {
                File::delete(public_path($users->avatar));
                $date = Str::slug(Carbon::now());
                $imageName = $userId . '-' . $date;
                Image::make($request->file('avatar'))->save(public_path('/uploads/users/') . $imageName . '.jpg')->encode('jpg', '50');
            }
            $fullname = $request->firstname . " " . $request->lastname;
            User::where('id', $userId)->update([
                'avatar' => $request->hasFile('avatar') ? '/uploads/users/' . $imageName . '.jpg' : $users->avatar,
                'name' => $fullname,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => $request->password == $users->password ? $users->password : Hash::make($request->password),
                'roll_id' => $request->roll_id,
                'roll' => 'customer',
                'user_status' => $request->user_status == 'on' ? 'on' : 'off',
            ]);
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'User Updated']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error !', 'content' => 'User could not be updated']);
        }
    }
}