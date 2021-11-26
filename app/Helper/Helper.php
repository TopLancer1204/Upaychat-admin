<?php

namespace App\Helper;

use App\Models\PendingSms;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
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

    public static function sendSMS($mobile, $msg, $type) //type-> 0:twilio, 1: multitexter
    {
        PendingSms::create([
            'mobile' => $mobile,
            'message' => $msg,
        ]);
        try {
            if($type == 0) {
                Helper::sendSMSTwilio($mobile, $msg);
            } else {
                Helper::sendSMSMultiTexter($mobile, $msg);
            }
            return ['success' => true, 'message' => "Sent success"];
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }
    public static function sendSMSMultiTexter($phone, $msg) {
        $token = env('MULTITEXTER_API_KEY');
        $sender_name = env('MULTITEXTER_SENDER');
        $recipients = preg_replace( '/[^0-9]/', '', $phone);

        $data = array("message"=>$msg, "sender_name"=>$sender_name, "recipients"=>$recipients, "forcednd"=>1);
        $data_string = json_encode($data);
        error_log($data_string);
        $ch = curl_init('https://app.multitexter.com/v2/app/sendsms');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$token));
        $result = curl_exec($ch);
        $res_array = json_decode($result);
        if($res_array->status == 1) {
            return;
        }
        Helper::sendSMSTwilio($phone, $msg);
        // throw new \Exception($res_array->msg, 1);
    }
    public static function sendSMSTwilio($phone, $msg) {
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_TOKEN");
        $twilio_number = env("TWILIO_FROM");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($phone, [
            'from' => $twilio_number, 
            'body' => $msg
            ]
        );
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
