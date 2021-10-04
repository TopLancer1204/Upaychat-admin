<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function getAdminEdit()
    {
        $user = auth()->user();
        $userId = $user->id;
        $roles = Role::all();

        return view('backend.profile.user-edit')->with('user', $user);
    }

    public function getAdminEditPass()
    {
        return view('backend.profile.admin-password');
    }

    public function savepassword(Request $request)
    {
        $user = auth()->user();

        if ((!$request->curpass) || (!$request->newpass) || (!$request->cnewpass)) {
            return response(['status' => 'error', 'title' => 'Error !', 'content' => 'Please enter current password and confirm new password']);
        }
        if (Hash::make($request->curpass) != $user->password) {
            return response(['status' => 'error', 'title' => 'Error !', 'content' => 'Current password is not valid']);
        }

        $user->password = Hash::make($request->newpass);
        $user->save();
        return response(['status' => 'success', 'title' => 'Success', 'content' => 'Password changed successfully']);
    }

    public function postUsersEdit(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        try {
            if ($request->hasFile('avatar')) {
                File::delete(public_path($user->avatar));
                $date = Str::slug(Carbon::now());
                $imageName = $userId . '-' . $date;
                Image::make($request->file('avatar'))->save(public_path('/uploads/users/') . $imageName . '.jpg')->encode('jpg', '50');
            }
            $fullname = $request->firstname . " " . $request->lastname;
            User::where('id', $userId)->update([
                'avatar' => $request->hasFile('avatar') ? '/uploads/users/' . $imageName . '.jpg' : $user->avatar,
                'name' => $fullname,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'mobile' => $request->mobile,
                'roll_id' => 1
            ]);
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Profile Updated']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error !', 'content' => 'Could not save the changes']);
        }
    }
}
