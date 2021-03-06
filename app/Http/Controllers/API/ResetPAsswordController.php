<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\sendMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ResetPAsswordController extends Controller
{
    use sendMessage;

    // RESET PASSWORD
    public function resetPassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ]);

        // Check from any errors in the request
        if($validator->fails())
            return $this->sendResponseError('Bad Request', $validator->errors(), 400);

        // Read the data from DB
        $user = User::where('email', $request->email)->first();

        // Reset the password
        $user->password = Hash::make($request->password);
        $user->save();

        // Create the token
        $result['id'] = $user->id;
        $result['name'] = $user->name;
        $result['token'] = $user->createToken('user@user')->accessToken;

        // Send the response
        return $this->sendResponseData($result, 'Password has been reset', 200);
    }

    public function changetPassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|min:8',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        // Check from any errors in the request
        if($validator->fails())
            return $this->sendResponseError('Bad Request', $validator->errors(), 400);

        // Read the data from DB
        $user = User::where('id', Auth::guard('api')->id())->first();

        // Check from the current password
        if(!Hash::check($request->old_password, $user->password))
            return $this->sendResponseMessage('Unauthorized', 401);

        // Change the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Send the response
        return $this->sendResponseMessage('Password has been change', 200);
    }
}
