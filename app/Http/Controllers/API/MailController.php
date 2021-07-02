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

        if($api_key == env("API_KEY_N")) {
            $details['logo'] = asset('images/logo_n.png');
            $details['team'] = 'Capsule application team';
            $details['app'] = 'Capsule';
        } else if($api_key == env("API_KEY_O")) {
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

        if($api_key == env("API_KEY_N")) {
            $details['logo'] = asset('images/logo_n.png');
            $details['team'] = 'Capsule application team';
            $details['app'] = 'Capsule';
        } else if($api_key == env("API_KEY_O")) {
            $details['logo'] = asset('images/logo_o.png');
            $details['team'] = 'TG Focus application team';
            $details['app'] = 'TG Focus';
        }

        Mail::to($email)->send(new SendForgetPasswordMail($details));
    }
}
