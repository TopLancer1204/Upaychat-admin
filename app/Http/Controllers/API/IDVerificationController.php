<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IDVerification;
use App\Models\IDVerificationMeta;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;

class IDVerificationController extends Controller
{
    public function index()
    {
        $userid = Auth::user()->id;
        $data = IDVerification::where('user_id', $userid)->first();
        if($data != null) {
            $data->metadata;
        }
        $response['status'] = "true";
        $response['data'] = $data;
        return response()->json($response);
    }
    public function verify(Request $request)
    {
        $user_id = Auth::user()->id;
        $exist = IDVerification::where('user_id', $user_id)->first();
        if($exist != null) {
            $verify_id = $exist->id;

            $fillable = (new IDVerification())->getFillable();
            IDVerification::where('user_id', $user_id)->update($request->merge(['status' => 0])->only($fillable));
            IDVerificationMeta::where('verify_id', $verify_id)->delete();
        } else {
            $verify_id = IDVerification::create($request->merge(['user_id' => $user_id])->all())->id;
        }

        if ($request->hasFile('gover_files')) {
            $files = $request->file('gover_files');
            foreach ($files as $key => $file) {
                $date = Str::slug(Carbon::now());
                $fileName = 'goverment_'.$date.'.'.$file->extension();
                $path = '/uploads/idverify/';
                if (!file_exists(public_path().$path)) mkdir(public_path().$path, 0777, true);
                $file->move(public_path().$path, $fileName);
                IDVerificationMeta::create([ 'verify_id' => $verify_id, 'type' => 0, "path" => $path.$fileName ]);
            }
        }
        if ($request->hasFile('proof_files')) {
            $files = $request->file('proof_files');
            foreach ($files as $key => $file) {
                $fileName = 'proof_'.$date.'.'.$file->extension();
                $path = '/uploads/idverify/';
                if (!file_exists(public_path().$path)) mkdir(public_path().$path, 0777, true);
                $file->move(public_path().$path, $fileName);
                IDVerificationMeta::create([ 'verify_id' => $verify_id, 'type' => 1, "path" => $path.$fileName ]);
            }
        }
        if ($request->hasFile('verify_files')) {
            $files = $request->file('verify_files');
            foreach ($files as $key => $file) {
                $fileName = "idcard_".$date.'.'.$file->extension();
                $path = '/uploads/idverify/';
                if (!file_exists(public_path().$path)) mkdir(public_path().$path, 0777, true);
                $file->move(public_path().$path, $fileName);
                IDVerificationMeta::create([ 'verify_id' => $verify_id, 'type' => 2, "path" => $path.$fileName ]);
            }
        }

        $response['status'] = true;

        return response()->json($response);
    }
}
