<?php

namespace App\Http\Controllers\API;

use App\Helper\Helper;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function forgotpassword(Request $request)
    {
        $mobile = $request->mobile_no;

        $user = User::firstWhere('mobile', $mobile);
        if ($user != null) {
            $user->password = Hash::make($request->password);
            $user->locked = 0;
            $user->save();
            ////////////////////////////////////
            $response['status'] = "true";
            $response['message'] = 'Password changed successfully';
            $response['data'] = [];
            return response()->json($response);
        } else {
            $response['status'] = "false";
            $response['message'] = 'No user found with mobile no ' . $mobile;
            $response['data'] = [];
            return response()->json($response);
        }
    }

    public function usersearch(Request $request)
    {
        $user = Auth::user();
        $extralist = array();
        if (isset($request['roll']) && $request['roll'] != '') {
            $model = User::select('id', 'avatar', 'firstname', 'lastname', 'username', 'email', 'mobile');
            if($request['roll'] == "all") {
                $userlist = $model->where('id', '!=', $user->id)->get();
            } else {
                $userlist = $model->where([/*['roll_id', 3], */ ['id', '!=', $user->id], ['roll', $request['roll']]])->get();
            }
        } else {
            $ids = Transaction::select("touser_id as id")
                ->where('user_id', $user->id)
                ->where('touser_id', '!=', $user->id);
            $uids = Transaction::select("user_id as id")
                ->where('touser_id', $user->id)
                ->where('user_id', '!=', $user->id)
                ->union($ids)
                ->get();

            $userlist = User::select('id', 'avatar', 'firstname', 'lastname', 'username', 'email', 'mobile')
                ->whereIn('id', $uids->pluck('id'))
                ->where([/*['roll_id', 3], */ ['id', '!=', $user->id]])->get();

            $extralist = User::select('id', 'avatar', 'firstname', 'lastname', 'username', 'email', 'mobile')
                ->whereNotIn('id', $uids->pluck('id'))
                ->where([/*['roll_id', 3], */ ['id', '!=', $user->id]])->get();
        }

        $ulist = array();
        foreach ($userlist as $u)
            $ulist[] = array(
                'user_id' => $u->id,
                'firstname' => $u->firstname,
                'lastname' => $u->lastname,
                'username' => $u->username,
                'email' => $u->email,
                'mobile' => $u->mobile,
                'profile_image' => $u->avatar,
                'extra' => false,
            );
        foreach ($extralist as $u)
            $ulist[] = array(
                'user_id' => $u->id,
                'firstname' => $u->firstname,
                'lastname' => $u->lastname,
                'username' => $u->username,
                'email' => $u->email,
                'mobile' => $u->mobile,
                'profile_image' => $u->avatar,
                'extra' => true,
            );
        $response['status'] = "true";
        $response['message'] = 'Search found ' . count($ulist) . ' records ';
        $response['data'] = $ulist;
        return response()->json($response);
    }

    public function deletepic(Request $request)
    {
        $user = Auth::user();
        try {
            $user->clearMediaCollection('profile_image');
            $response['status'] = "true";
            $response['message'] = " Image removed successfully";
            $response['data'] = '';
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = " Image could not be removed";
            $response['data'] = '';
            return response()->json($response);
        }
    }

    public function checkemail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
        ]);
        if ($validator->fails()) {
            $errmess = implode(', ', $validator->messages()->all());
            $response['status'] = "false";
            $response['message'] = $errmess;
            $response['data'] = [];
            return response()->json($response);
        } else {
            $code = Helper::generateRandomNumber();
            Helper::sendEmail($request->email, "Your UpayChat Code is " . $code.". It expires in 5 minutes.", "UpayChat Email Verificaiton");
            $response['message'] = $code;
            $response['status'] = "true";
            return response()->json($response);
        }
    }

    public function checkmobile(Request $request)
    {
        if ($request->exist == 'false') {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|unique:users',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|exists:users',
            ]);
        }
        if ($validator->fails()) {
            $errmess = implode(', ', $validator->messages()->all());
            $response['status'] = "false";
            $response['message'] = $errmess;
        } else {
            $code = Helper::generateRandomNumber();
            $istwilio = $request->twilio;
            $type = 1;
            if($istwilio === true || $istwilio === "true") {
                $type = 0;
            }
            $res = Helper::sendSMS($request->mobile, "Your UpayChat Code is " . $code.". It expires in 5 minutes.", $type);
            if($res['success']) {
                $response['status'] = "true";
                $response['message'] = $code;
            } else {
                $response['status'] = "false";
                $response['message'] = $res['message'];
            }
        }
        $response['data'] = [];
        return response()->json($response);
    }

    public function register(Request $request)
    {
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader != 'application/json') {
            return response()->json(array('error' => 'Wrong custom header'), 400);
        }

        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'fcm_token' => 'required',
            'birthday' => 'required',
        ]);
        if ($validator->fails()) {
            $errmess = implode(', ', $validator->messages()->all());
            $response['status'] = "false";
            $response['message'] = $errmess;
            return response()->json($response);
        }

        $input = $request->merge(['email_verified_at' => now()])->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        if ($request->has('profile_image')) {
            $date = Str::slug(Carbon::now());
            $imageName = $user->id . '-' . $date;
            Image::make($request->file('profile_image'))->save(public_path('/uploads/users/') . $imageName . '.jpg')->encode('jpg', '50');
            $user->avatar = '/uploads/users/' . $imageName . '.jpg';
        }
        $user->save();

        $uid = $user->id;
        $wallet = Wallet::create([
            'user_id' => $uid,
            'balance' => '0.00',
        ]);

        $pendings = Transaction::where([['touser_id', $request->email], ['status', 4]])
            // ->orWhere([['touser_id', $request->mobile], ['status', 4]])
            ->get();
        $message = "";
        foreach ($pendings as $pending) {
            $pending->touser_id = $uid;

            $sender = User::find($pending->user_id) ?? (object)['firstname' => "#", "lastname" => "" ];

            if (strtolower($pending->transaction_type) == 'pay') {
                $pending->status = 1;
                // update this user's wallet
                $wallet->balance += $pending->amount;

                $message = $sender->firstname . " " . $sender->lastname . " paid you ₦" . number_format($pending->amount, 2, '.', ',');
            } elseif (strtolower($pending->transaction_type) == 'request') {
                $pending->status = 0;
                $message = $sender->firstname . " " . $sender->lastname . " requested ₦" . number_format($pending->amount, 2, '.', ',') . " from you";
            }
            $pending->save();

            ////////////////////// notification for receiver ///////////////
            $Noti = new Notification;
            $Noti->post_id = $pending->id;
            $Noti->user_id = $pending->touser_id;
            $Noti->notification = $message;
            $Noti->save();
        }
        $wallet->save();

        $user = User::find($uid);

        $userdata = array(
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'username' => $user->username,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'roll' => $user->roll,
            'profile_image' => $user->avatar,
        );

        $response['status'] = "true";
        $response['message'] = "Successfully registered.";
        $response['token'] = $user->createToken(config('vms.myToken'))->accessToken;
        $response['data'] = $userdata;

        return response()->json($response);
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
        }
    }

    public function login(Request $request)
    {
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader != 'application/json') {
            $response['status'] = "false";
            $response['message'] = "Not a valid API request.";
            $response['token'] = "";
            $response['data'] = [];
            return response()->json($response);
        }
        if (!($request->login_user) || (!$request->password)) {
            $response['status'] = "false";
            $response['message'] = 'Missing params';
            $response['data'] = [];
            return response()->json($response);
        }
        $login_user =$request->input('login_user');
        $current_user = User::where('email', $login_user)->orwhere('mobile', $login_user)->orwhere('username', $login_user)->first();
        if($current_user != null && $current_user->locked >= 5) {
            $response['status'] = "false";
            $response['message'] = 'Your account is locked, Please change your password.';
            $response['data'] = [];
            return response()->json($response);
        }

        $login_type = filter_var($login_user, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$login_type => $login_user, 'password' => request('password')]) ||
            ($login_type == 'username' &&
                (Auth::attempt(['mobile' => $login_user, 'password' => request('password')]) ||
                    Auth::attempt(['mobile' => '+' . $login_user, 'password' => request('password')])))) {
            $user = Auth::user();
            // if($user->user_status == "off") {
            //     $response['status'] = "false";
            //     $response['message'] = "Your account suspended.";
            //     $response['token'] = "";
            //     $response['data'] = [];
            // } else {
            $user->locked = 0;
            $user->fcm_token = $request->fcm_token;
            $user->save();
            $userdata = array(
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'username' => $user->username,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'roll' => $user->roll,
                'profile_image' => $user->avatar,
                'birthday' => $user->birthday,
            );

            $response['status'] = "true";
            $response['message'] = "Successfully logged in.";
            $response['token'] = $user->createToken(config('vms.myToken'))->accessToken;
            $response['data'] = $userdata;
            // }
    } else {
            if($current_user != null) {
                $current_user->locked = $current_user->locked + 1;
                $current_user->save();
            }
            $response['status'] = "false";
            $response['message'] = "Invalid username or password.";
            $response['token'] = "";
            $response['data'] = [];
        }
        return response()->json($response);
    }

    public function updateprofile(Request $request)
    {
        $data = $request->all();
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader != 'application/json') {
            return response()->json(array('error' => 'Wrong custom header'), 400);
        }
        $user = Auth::user();

        try {
            if ($request->has('mobile')) {
                if($user->mobile == null || $user->mobile == "") {
                    $wallet = Wallet::where('user_id', $user->id);
                    if($wallet == null || $wallet->count() <= 0) {
                        $wallet = Wallet::create([ 'user_id' => $user->id, 'balance' => '0.00' ]);
                    } else {
                        $wallet = $wallet->first();
                    }
                    $pendings = Transaction::where([['touser_id', $data['mobile']], ['status', 4]])->get();
            
                    foreach ($pendings as $pending) {
                        $pending->touser_id = $user->id;
            
                        $sender = User::find($pending->user_id) ?? (object)['firstname' => "#", "lastname" => "" ];
            
                        if (strtolower($pending->transaction_type) == 'pay') {
                            $pending->status = 1;
                            // update this user's wallet
                            $wallet->balance += $pending->amount;
            
                            $message = $sender->firstname . " " . $sender->lastname . " paid you ₦" . number_format($pending->amount, 2, '.', ',');
                        } elseif (strtolower($pending->transaction_type) == 'request') {
                            $pending->status = 0;
                            $message = $sender->firstname . " " . $sender->lastname . " requested ₦" . number_format($pending->amount, 2, '.', ',') . " from you";
                        }
                        $pending->save();
            
                        ////////////////////// notification for receiver ///////////////
                        $Noti = new Notification;
                        $Noti->post_id = $pending->id;
                        $Noti->user_id = $pending->touser_id;
                        $Noti->notification = $message;
                        $Noti->save();
                    }
                    $wallet->save();
                }
                $user->mobile = $data['mobile'];
            }
            if ($request->has('firstname')) $user->firstname = $data['firstname'];
            if ($request->has('lastname')) $user->lastname = $data['lastname'];
            if ($request->has('birthday')) $user->birthday = $data['birthday'];

            if ($request->has('profile_image')) {
                File::delete(public_path($user->avatar));
                $date = Str::slug(Carbon::now());
                $imageName = $user->id . '-' . $date;
                Image::make($request->file('profile_image'))->save(public_path('/uploads/users/') . $imageName . '.jpg')->encode('jpg', '50');
                $user->avatar = '/uploads/users/' . $imageName . '.jpg';
            }
            $user->save();

            $response['status'] = "true";
            $response['message'] = "Profile updated successfully.";
            $response['firstname'] = $user->firstname;
            $response['lastname'] = $user->lastname;
            $response['birthday'] = $user->birthday;
            $response['mobile'] = $user->mobile;
            $response['profile_image'] = $user->avatar;

            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();//"System Error ";
            $response['data'] = '';
            return response()->json($response);
        }
    }

    public function changepassword(Request $request)
    {
        if (!($request->new_password) || (!$request->password)) {
            $response['status'] = "false";
            $response['message'] = "Missing param";
            $response['token'] = "";
            $response['data'] = '';
            return response()->json($response);
        }
        $user = Auth::user();

        //param1 - user password that has been entered on the form
        //param2 - old password hash stored in database
        if (Hash::check($request->password, $user->password)) {
            $newpass = Hash::make($request->new_password);

            $user->password = $newpass;
            $user->locked = 0;
            $user->save();

            $response['status'] = "true";
            $response['message'] = "Password changed successfully";
            $response['data'] = '';
            return response()->json($response);
        } else {
            $response['status'] = "false";
            $response['message'] = "Invalid current password";
            $response['data'] = [];
            return response()->json($response);
        }
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user]);
    }
}