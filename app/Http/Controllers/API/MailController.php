<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\SendForgetPasswordMail;
use App\Mail\SendRegistrationMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    // SEND REGISTRATION VERIFICATION CODE
    public function mailRegistrationVerify($name, $email, $code, $api_key)
    {
        $details = [
            'name' => $name,
            'code' => $code,
        ];

        if($api_key == "45reg4rhe54bgr4eryki58fqz5f5t") {
            $details['logo'] = asset('images/logo_n.png');
            $details['team'] = 'Capsule application team';
            $details['app'] = 'Capsule';
        } else if($api_key == "5c4e878thbg5n4j54ii7sx4q5xad4") {
            $details['logo'] = asset('images/logo_o.png');
            $details['team'] = 'TG Focus application team';
            $details['app'] = 'TG Focus';
        }

        Mail::to($email)->send(new SendRegistrationMail($details));
    }

    // SEND CODE IN EMAIL FOR FORGET PASSWORD
    public function mailForgetPassword($name, $email, $code, $api_key)
    {
        $details = [
            'name' => $name,
            'code' => $code,
        ];

        if($api_key == "45reg4rhe54bgr4eryki58fqz5f5t") {
            $details['logo'] = asset('images/logo_n.png');
            $details['team'] = 'Capsule application team';
            $details['app'] = 'Capsule';
        } else if($api_key == "5c4e878thbg5n4j54ii7sx4q5xad4") {
            $details['logo'] = asset('images/logo_o.png');
            $details['team'] = 'TG Focus application team';
            $details['app'] = 'TG Focus';
        }

        Mail::to($email)->send(new SendForgetPasswordMail($details));
    }
}
