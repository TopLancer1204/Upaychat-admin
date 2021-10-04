<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SliderController extends Controller
{
    public function getSliders()
    {
        $sliders = Slider::all();
        return view('backend.sliders.sliders')->with('sliders', $sliders);
    }

    public function getSlidersAdd()
    {
        return view('backend.sliders.slider-add');
    }

    public function getSlidersEdit($sliderId)
    {
        $slider = Slider::where('id', $sliderId)->first();
        return view('backend.sliders.slider-edit')->with('slider', $slider);
    }


    public function postSliders(Request $request)
    {
        if (isset($request->delete)) {
            try {
                $sliders = Slider::where('id', $request->id)->first();
                File::delete(public_path($sliders->slider_image));
                Slider::where('id', $request->id)->delete();
                return response(['status' => 'success', 'title' => 'Success', 'content' => 'Images deleted']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Error', 'content' => 'Image could be deleted']);
            }
        } elseif (isset($request->slider_status)) {
            try {
                Slider::where('id', $request->id)->update($request->all());
                return response(['status' => 'success', 'title' => 'Success', 'content' => 'Status changed successfully']);
            } catch (\Exception $e) {
                return response(['status' => 'error', 'title' => 'Error', 'content' => 'Status could not be changed']);
            }
        }
    }

    public function postSlidersAdd(Request $request)
    {
        try {
            $date = Str::slug(Carbon::now());
            $imageName = Str::slug($request->slider_name) . '-' . $date;
            Image::make($request->file('image'))->save(public_path('/uploads/sliders/') . $imageName . '.jpg')->encode('jpg', '50');
            $request->merge(['slider_image' => '/uploads/sliders/' . $imageName . '.jpg']);
            $request->merge(['slider_status' => $request->slider_status == 'on' ? 'on' : 'off']);
            Slider::create($request->all());
            return response(['status' => 'success', 'title' => 'Success', 'content' => 'Image added successfully']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Error', 'content' => 'Error while adding image']);
        }
    }

    public function postSlidersEdit(Request $request, $sliderId)
    {
        try {
            $sliders = Slider::where('id', $sliderId)->first();
            if ($request->hasFile('slider_image')) {
                File::delete(public_path($sliders->slider_image));
                $date = Str::slug(Carbon::now());
                $imageName = Str::slug($request->slider_name) . '-' . $date;
                Image::make($request->file('slider_image'))->save(public_path('/uploads/sliders/') . $imageName . '.jpg')->encode('jpg', '50');
            }

            Slider::where('id', $sliderId)->update([
                'slider_name' => $request->slider_name,
                'slider_image' => $request->hasFile('slider_image') ? '/uploads/sliders/' . $imageName . '.jpg' : $sliders->slider_image,
                'slider_url' => $request->slider_url,
                'slider_status' => $request->slider_status == 'on' ? 'on' : 'off',
            ]);

            return response(['status' => 'success', 'title' => 'Başarılı', 'content' => 'Slider Güncellendi ']);
        } catch (\Exception $e) {
            return response(['status' => 'error', 'title' => 'Başarısız', 'content' => 'Slider Güncellenemedi']);
        }
    }
}
