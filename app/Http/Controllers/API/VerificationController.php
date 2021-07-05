<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\MailController;
use App\Http\Traits\sendMessage;
use App\Http\Traits\VerifyCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VerificationController extends MailController
{
    use VerifyCode, sendMessage;

    // VERIFICATION THE EMAIL ADDRESS
    public function emailRegisterVerify(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'code' => 'required|numeric|between:1000,9999'
        ]);

        // Check from any errors
        if($validator->fails())
            return $this->sendResponseError('Bad Request', $validator->errors(), 400);

        // Read the data from DB
        $user = User::where('id', Auth::guard('api')->id())->first();

        // Check if the email is already verified
        if($user->email_verified_at)
            return $this->sendResponseMessage('Already Reported', 208);

        // Check if the verification code is correct
        if($request->code != $user->code_verify)
            return $this->sendResponseMessage('Unauthorized ', 401);

        // Verify the email address
        $user->email_verified_at = Carbon::now();
        $user->save();

        // Send a message
        return $this->sendResponseMessage('Verified Successful', 200);
    }

    // SEND A VERIFICATION CODE
    public function codeSendVerify(Request $request)
    {
        // Read the data from DB
        $user = User::where('id', Auth::id())->first();

        // Get a new generate number
        $code = $this->generate();

        // Send a new mail to verify the registration
        $this->mailRegistrationVerify($user->name, $user->email, $code, $request->api_key);

        // Update the verification code in DB
        $user->code_verify = $code;
        $user->save();

        // Send a message
        return $this->sendResponseMessage('Code has been sent', 200);
    }
}
