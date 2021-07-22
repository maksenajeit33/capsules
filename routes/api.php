<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\ForgetPasswordController;
use App\Http\Controllers\API\ResetPAsswordController;
use App\Http\Controllers\API\CapsuleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/user')->namespace('API')->middleware('api_key')->group(function () {
    Route::middleware('guest:api')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        // This Routes are for forget password
        Route::post('email/forget/message', [ForgetPasswordController::class, 'emailForgetMessage']);
        Route::post('email/forget/code', [ForgetPasswordController::class, 'emailForgetCode']);
        // This route is for change the password
        Route::post('password/reset', [ResetPAsswordController::class, 'resetPassword']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        // This Routes are for register verification code
        Route::post('email/verify/register', [VerificationController::class, 'emailRegisterVerify']);
        Route::post('email/verify/code', [VerificationController::class, 'codeSendVerify']);
        // This route is for reset the password
        Route::post('password/change', [ResetPAsswordController::class, 'changetPassword']);
        // This route is for increase the number of capsules and get the report
        Route::post('capsules/plus', [CapsuleController::class, 'capsulesPlus']);
        Route::get('capsules/report', [CapsuleController::class, 'capsulesReport']);
        // This routes are for set and get the current counter type
        Route::put('counter/set', [CapsuleController::class, 'counterSet']);
        Route::get('counter/get', [CapsuleController::class, 'counterGet']);
        // Return the info about user
        Route::get('info', function (Request $request) {
            $info = $request->user();
            return response()->json([
                'id' => $info['id'],
                'name' => $info['name'],
                'email' => $info['email'],
            ]);
        });
    });
});
