<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function mynotification(Request $request)
    {
        $user = Auth::user();
        $userid = $user->id;

        $notifications = Notification::where('user_id', $userid)->where('status', 1)->orderBy('created_at', 'DESC')->get();
        $nlist = array();

        if (count($notifications) < 1)
            $response['message'] = 'No notifications';
        else {
            foreach ($notifications as $notification)
                $nlist[] =
                    array(
                        'id' => $notification->id,
                        'notification' => $notification->notification,
                        'created_at' => $notification->created_at
                    );
            $response['message'] = 'Success';
        }
        $response['data'] = $nlist;
        $response['status'] = "true";
        return response()->json($response);
    }

    public function closenotification(Request $request)
    {
        $notiId = $request->notification_id;
        try {
            $notification = Notification::find($notiId);
            $notification->status = 0;
            $notification->save();

            $response['status'] = "true";
            $response['message'] = 'Success';
            $response['data'] = [];
            return response()->json($response);
        } catch (\Exception $e) {
            $response['status'] = "false";
            $response['message'] = $e->getMessage();
            $response['data'] = [];
            return response()->json($response);
        }
    }
}
