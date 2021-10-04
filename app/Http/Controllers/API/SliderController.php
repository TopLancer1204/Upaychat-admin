<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Slider;

class SliderController extends Controller
{
    public function sliderList()
    {
        $result = Slider::select('id', 'slider_name', 'slider_image')->get();

        if ($result) {
            $response['status'] = "true";
            $response['message'] = "Slider List";
            $response['data'] = $result;
        } else {
            $response['status'] = "false";
            $response['message'] = "Something went wrong. Please Try again.";
            $response['data'] = [];
        }
        return response()->json($response);
    }
}
