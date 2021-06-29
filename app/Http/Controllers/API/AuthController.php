<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\SendResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use SendResponse;

    // REGISTER USER
    public function register(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        // Check from errors in the validation
        if($validator->fails())
            return $this->sendResponseError('Please validate the errors', $validator->errors(), 400);

        // Create user and token
        $value = $request->all();
        $value['password'] = Hash::make($value['password']);
        $user = User::create($value);
        $result['name'] = $user->name;
        $result['token'] = $user->createToken('user@user')->accessToken;

        // Send the response
        return $this->sendResponseData($result, 'Register successful', 201);
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
            return $this->sendResponseError('Please validate the errors', $validator->errors(), 400);

        // Read the data from database
        $value = $request->all();
        $user = User::where('email', $value['email'])->first();

        // Check from the password
        if(!Hash::check($value['password'], $user->password))
            return $this->sendResponseError('Login Failed', '', 400);

        // Create token
        $result['name'] = $user->name;
        $result['token'] = $user->createToken('user@user')->accessToken;

        // Send the response
        return $this->sendResponseData($result, 'Login successful', 200);
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
        return $this->sendResponseMessage('Logout successful', 200);
    }
}
