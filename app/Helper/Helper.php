<?php

namespace App\Helper;

use App\Models\PendingSms;
use Illuminate\Support\Facades\Mail;

class Helper
{
    public static function generateRandomNumber($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        return $randomString;
    }

    public static function sendEmail($email, $msg, $subject)
    {
        try {
            $data = array('msg' => $msg);

            Mail::send('backend.transactions.mail', $data, function ($message) use ($email, $subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($email);
                $message->subject($subject);
            });
        } catch (\Throwable $th) {
        }
    }

    public static function sendSMS($mobile, $msg)
    {
        PendingSms::create([
            'mobile' => $mobile,
            'message' => $msg,
        ]);
    }

    public static function sendPushNotification($notification_id, $title, $message, $image = null, $icon = null)
    {
        $notification['to'] = $notification_id;
        $notification['priority'] = 'high';
        $notification['notification']['title'] = $title;
        $notification['notification']['body'] = $message ?: 'New message';
        if ($image) $notification['notification']['image'] = $image;
        if ($icon) $notification['notification']['icon'] = $icon;
        $notification['notification']['sound'] = true;

        $crl = curl_init();

        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: key=' . env('FCM_KEY');
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($crl, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);

        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, json_encode($notification));
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($crl);

        curl_close($crl);

        return $response == false;
    }
}
