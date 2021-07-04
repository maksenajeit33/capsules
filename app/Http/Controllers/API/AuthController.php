<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\MailController;
use App\Http\Traits\sendMessage;
use App\Http\Traits\VerifyCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends MailController
{
    use sendMessage, VerifyCode;

    // REGISTER USER
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        // Check from errors in the validation
        if($validator->fails())
            return $this->sendResponseError('register failed', $validator->errors(), 400);

        // Get a generate number to send it in email and stored in DB
        $code = $this->generate();

        // Create user and token
        $value = $request->all();
        $value['password'] = Hash::make($value['password']);
        $value['code_verify'] = $code;
        $user = User::create($value);
        $result['name'] = $user->name;
        $result['token'] = $user->createToken('user@user')->accessToken;

        // Send a mail to verify the registration
        $this->mailRegistrationVerify($user->name, $user->email, $code, $request->api_key);

        // Send the response
        return $this->sendResponseData($result, 'register success', 202);
    }

    // LOGIN USER
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8'
        ]);

        // Check from errors in the validation
        if($validator->fails())
            return $this->sendResponseError('login failed', $validator->errors(), 400);

        // Read the data from database
        $value = $request->all();
        $user = User::where('email', $value['email'])->first();

        // Check from the password
        if(!Hash::check($value['password'], $user->password))
            return $this->sendResponseError('login failed', '', 406);

        // Create token
        $result['name'] = $user->name;
        $result['token'] = $user->createToken('user@user')->accessToken;

        // Set a message to send it to user
        if($user->hasVerifiedEmail()) {
            $message = 'login success';
            $code = 202;
        } else {
            $message = 'email not verify';
            $code = 403;
        }

        // Send the response
        return $this->sendResponseData($result, $message, $code);
    }

    // LOGOUT USER
    public function logout(Request $request)
    {
        // Revoke access token
        $request->user()->token()->revoke();

        // Revoke all of the token's refresh tokens
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($request->user()->token()->id);

        // Send the response
        return $this->sendResponseMessage('logout success', 200);
    }
}
