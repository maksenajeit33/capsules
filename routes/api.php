<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\ForgetPasswordController;

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

Route::prefix('user')->namespace('API')->middleware('api_key')->group(function () {
    Route::middleware('guest:api')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        // This Routes are for forget password
        Route::post('email/forget/message', [ForgetPasswordController::class, 'emailForgetMessage']);
        Route::post('email/forget/code', [ForgetPasswordController::class, 'emailForgetCode']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        // This Routes are for register verification code
        Route::post('email/verify/register', [VerificationController::class, 'emailRegisterVerify']);
        Route::post('email/verify/code', [VerificationController::class, 'codeSendVerify']);
    });
});
