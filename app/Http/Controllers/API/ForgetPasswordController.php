<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\MailController;
use App\Http\Traits\sendMessage;
use App\Http\Traits\VerifyCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ForgetPasswordController extends MailController
{
    use sendMessage, VerifyCode;

    // THIS METHOD IS FOR SEND AN EMAIL FOR FORGET PASSWORD
    public function emailForgetMessage(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        // Check from any error in validation
        if($validator->fails())
            return $this->sendResponseError('something went wrong', $validator->errors(), 400);

        // Read the data from DB
        $user = User::where('email', $request->email)->first();

        // get a generate number
        $code = $this->generate();

        // Send an email
        $this->mailForgetPassword($user->name, $user->email, $code, $request->api_key);

        // Save the generate number in DB
        $user->code_verify = $code;
        $user->save();

        // Return the response
        return $this->sendResponseMessage('code sent success', 200);
    }

    // THIS METHOD IS FOR CHECK FROM THE CODE IN THE FORGET PASSWORD
    public function emailForgetCode(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric|between:1000,9999'
        ]);

        // Check from any errors in the validation
        if($validator->fails())
            return $this->sendResponseError('something went wrong', $validator->errors(), 400);

        // Read the data from DB
        $user = User::where('email', $request->email)->first();

        // Check from the code is correct or not
        if($user->code_verify != $request->code)
            return $this->sendResponseError('the code is incorrect', '', 406);

        // Check if the email address is verified, if no, then verify it
        if(!$user->email_verified_at) {
            $user->email_verified_at = Carbon::now();
            $user->save();
        }

        // Send the response
        return $this->sendResponseMessage('verified success', 202);
    }
}
