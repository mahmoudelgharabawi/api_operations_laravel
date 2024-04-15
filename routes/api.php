<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::Post('auth/register',[\App\Http\Controllers\Api\Auth\AuthController::class,'register'] );
Route::Post('auth/login',[\App\Http\Controllers\Api\Auth\AuthController::class,'login'] );
Route::Post('auth/verify_user_email',[\App\Http\Controllers\Api\Auth\AuthController::class,'verifyUserEmail'] );
Route::Post('auth/resend_email_verification_link',[\App\Http\Controllers\Api\Auth\AuthController::class,'resendEmailVerificationLink'] );


Route::middleware(['auth'])->group(function (){
    Route::post('/change_password',[\App\Http\Controllers\Api\Profile\PasswordController::class,'changeUserPassword']);
});
