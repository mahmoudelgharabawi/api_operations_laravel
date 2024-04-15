<?php

namespace App\Http\Controllers\Api\Auth;

use App\Customs\Services\EmailVerificationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendEmailVerificationLinkRequest;
use App\Http\Requests\VerifyEmailRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{


    public function __construct(private EmailVerificationService $service)
    {
    }


    public function login(LoginRequest $request) {
        $token = auth()->attempt($request->validated());
        if($token){
            return $this->responseWithToken($token,auth()->user());
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid Credentials',
            ],401);
        }

    }
    public function register(RegisterRequest $request) {
        $user = User::create($request->validated());
        if ($user) {
            // to send verification code
            $this->service->sendVerificationLink($user);
            $token = auth()->login($user);
            return $this->responseWithToken($token,$user);
        }else {
            return response()->json([
                'status' => 'failed',
                'message' => 'An Error While Creating User',
            ],500);
        }
    }

    public function responseWithToken($token,$user) {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
            'type' => 'bearer',

        ]);
    }

    public function verifyUserEmail(VerifyEmailRequest $request) {
        return $this->service->verifyEmail($request->email,$request->token);
    }

    public function resendEmailVerificationLink(ResendEmailVerificationLinkRequest $request){
        return $this->service->resendLink($request->email);
    }






}
