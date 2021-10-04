<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SettingController extends Controller
{
    public function getSettings(){
        $settings = Setting::all();
        return view('backend.settings.settings')->with('settings',$settings);
    }

    public function getSettingsEdit($settingId){
        $setting = Setting::where('id',$settingId)->first();
        return view('backend.settings.setting-edit')->with('setting',$setting);
    }

    public function postSettings(Request $request)
    {
        if (isset($request->setting_description)){
            try {
                Setting::create($request->all());
                return response(['status' => 'success', 'title' => 'Başarılı', 'content' => 'Ayar Eklendi']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Başarısız', 'content' => 'Ayar Eklenemedi']);
            }
        }
        else{
            try {
                $settings =  Setting::where('id',$request->id)->first();
                if ($settings->setting_type == 'img'){
                    File::delete(public_path($settings->setting_value));
                }
                Setting::where('id',$request->id)->delete();
                return response(['status' => 'success', 'title' => 'Başarılı', 'content' => 'Ayar Silindi']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Başarısız', 'content' => 'Ayar Silinemedi']);
            }
        }

    }

    public function postSettingsEdit(Request $request,$settingId){
        try {
            $settings =  Setting::where('id',$settingId)->first();
            $date = Str::slug(Carbon::now());
            $imageName = Str::slug($settings->setting_key) . '-' . $date;
            if ($request->hasFile('setting_value')) {
                File::delete(public_path($settings->setting_value));
                Image::make($request->file('setting_value'))->save(public_path('/uploads/settings/') . $imageName . '.jpg')->encode('jpg', '50');

                Setting::where('id',$settingId)->update([
                    'setting_value' => $request->hasFile('setting_value') ? '/uploads/settings/' . $imageName . '.jpg' : $settings->setting_key,
                ]);
            }
            else {
                Setting::where('id',$settingId)->update([
                    'setting_value' => $request->setting_value,
                ]);
            }
            return response(['status' => 'success', 'title' => 'Başarılı', 'content' => 'Ayar Güncellendi']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Başarısız', 'content' => 'Ayar Güncellenemedi']);
        }
    }
}
