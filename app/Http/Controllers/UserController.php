<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use App\Helper\JWTToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    function userRegistration(Request $request)
    {
        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successfully'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => 'fail',
                'message' => $exception->getMessage(),
            ]);
        }
    }


    function userLogin(Request $request)
    {
        $count = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->count();

        if ($count == 1) {
            //user login korabo and jwt token create korbo
            $token = JWTToken::CreateToken($request->input('email'));
            return response()->json([
                'status' => 'success',
                'message' => 'User login successful',
                'token' => $token
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'unathorized'
            ]);
        }
    }

    function SendOTPCode(Request $request)
    {
        $email = $request->input('email');
        $otp = rand(1000,9999);
        $count = User::where('email', '=', $email)->count();

        if ($count == 1) {
            //OTP email e send korbo
            Mail::to($email)->send(new OTPMail($otp));
            //OTP db table e save korbo
            User::where('email','=',$email)->update(['otp'=>$otp]);

            return response()->json([
                'status'=>'success',
                'message'=>'Otp send'
            ]);

           
        } else {
            return response()->json([
                'status'=>'failed',
                'message'=>'Otp send Failed'
            ]);
        }
    }
}
