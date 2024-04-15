<?php

namespace App\Customs\Services;

use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EmailVerificationService{

    public function verifyToken(string $email,string $token){
        $token = EmailVerificationToken::where('email',$email)->where('token',$token)->first();
        if ($token) {
            if ($token->expired_at >= now()){
                return $token;
            }else {
                $token->delete();
                response()->json([
                    'status' => 'failed',
                    'message' => 'Token Expired'
                ])->send();
                exit;
            }
        }else {
            response()->json([
                'status' => 'failed',
                'message' => 'Invalid Token'
            ])->send();
            exit;
        }
    }


    public function checkIfEmailIsVerified($user){
        if ($user->email_verified_at) {
            response()->json([
                'status' => 'failed',
                'message' => 'Invalid Token'
            ])->send();
            exit;
        }
    }

    public function resendLink($email){
        $user = User::where('email',$email)->first();
        if ($user) {
            $this->sendVerificationLink($user);
            return response()->json([
                'status' => 'success',
                'message' => 'verification link send successfully'
            ]);
        }else{
         return response()->json([
                'status' => 'failed',
                'message' => 'User Not Found'
            ]);
        }
    }

    public function verifyEmail(string $email,string$token){
        $user = User::where('email',$email)->first();
        if (!$user){
            response()->json([
                'status' => 'failed',
                'message' => 'User not found'
            ])->send();
            exit;
        }
        $this->checkIfEmailIsVerified($user);
        $verifiedToken = $this->verifyToken($email,$token);

        if($user->markEmailAsVerified()){
            $verifiedToken->delete();
            response()->json([
                'status' => 'success',
                'message' => 'Email has been verified successfully'
            ]);
        }else{
            response()->json([
                'status' => 'failed',
                'message' => 'Email verification failed, please try again later.'
            ]);
        }


    }



    public function sendVerificationLink(object $user) : void {
       Notification::send($user,new EmailVerificationNotification($this->generateVerificationLink($user->email)));
    }




    public function generateVerificationLink(string $email) {
        $checkTokenExists = EmailVerificationToken::where('email',$email)->first();
        if($checkTokenExists) $checkTokenExists->delete();
        $token = Str::uuid();
        $url = config('app.url')."?token=".$token ."&email=".$email;
        $saveToken = EmailVerificationToken::create([
            "email" => $email,
            "token" => $token,
            "expired_at" => now()->addMinutes(60)
        ]);
        if ($saveToken) {
            return $url;
        }
    }
}
