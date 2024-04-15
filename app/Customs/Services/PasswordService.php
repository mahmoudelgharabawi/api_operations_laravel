<?php

namespace App\Customs\Services;


use Illuminate\Support\Facades\Hash;

class PasswordService {

    private function validateCurrentPassword($current_password){
        if (!password_verify($current_password,auth()->user()->password)){
            response()->json([
                'status' => 'failed',
                'message' => 'Password did not match the current password'
            ])->send();
            exit;
        }
    }


    public function changePassword($data) {
        $this->validateCurrentPassword($data['current_password']);
       $updatePassword =  auth()->user()->update([
           'password' => Hash::make($data['password'])
       ]);

       if ($updatePassword) {
         return response()->json([
               'status' => 'success',
               'message' => 'password updated successfully'
           ]);
       }else {
           return response()->json([
               'status' => 'failed',
               'message' => 'An error occured while updating password'
           ]);
       }
    }



}
