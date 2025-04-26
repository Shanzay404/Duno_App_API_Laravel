<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;

class forgotPasswordController extends Controller
{
    
    public function forgotPassword(Request $request){
    try{
            $validateUser=Validator::make(
                $request->all(),
                ['email'=>'required|email|exists:users,email']
            );
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication Error',
                    'errors' => $validateUser->errors()->first()
                ],404);
            }

            $otp = mt_rand(1000,9999);
            $saveOtp = DB::table('password_resets')->insert([
                'email'=>$request->email,
                'otp'=>$otp,
                'created_at'=>Carbon::now(),
            ]);

            Mail::raw("Reset Password Otp is: $otp", function($message) use ($request){
                $message->to($request->email)
                    ->subject('Reset Password');
            });

            return response()->json([
                'status' => true,
                'message' =>  'Your OTP has been sent to your registered email address.',
                'OTP' => $otp,
            ],201);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => "something went's Wrong. Please try again",
                'errors' => $e->getMessage(),
            ], 500);
        }
    }


    // resend opt same process so we use the above function
    

    // submit form with new otp

    // public function submitResetPasswordForm(Request $request){
    //     try{
    //     $validateUser=Validator::make(
    //         $request->all(),
    //         [
    //             'email' => 'required|email|exists:users,email',
    //             'password' => 'required|confirmed|min:5|',
    //             'password_confirmation' => 'required',
    //             'otp' => 'required'
    //         ]
    //         );

    //         if($validateUser->fails()){
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Authentication Error',
    //                 'errors' => $validateUser->errors()->first()
    //             ],404);
    //         }

    //         $otpExists = DB::table('password_resets')
    //                         ->where([
    //                             'email'=> $request->email, 'otp' => $request->otp
    //                         ])->first();
    //         if(!$otpExists){
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Please Enter a Valid Otp',
    //                 'errors' => $validateUser->errors()->first()
    //             ],404);
    //         }

    //         $optCreatedAt = Carbon::parse($otpExists->created_at);
    //         if($optCreatedAt->diffInMinutes(Carbon::now()) > 1){
    //             return response()->json(
    //                 [
    //                     'status' => false,
    //                     'message' => "Your Otp has been Expired. Please Request a new one.",
    //                 ],404);
    //         }

    //         // Update Password
    //         User::where('email', $request->email)->update(['password'=>Hash::make($request->password)]);
    //         $user = User::where('email', $request->email)->get();
    //         DB::table('password_resets')->where(['email'=> $request->email])->delete();

    //         return response()->json([
    //           'status' => true,
    //           'message' =>  'Your Password Has been Changed',
    //            'user' => $user,
    //        ],201);
    //     }
    //        catch (\Exception $e)
    //        {
    //            return response()->json([
    //                'status' => false,
    //                'message' => "something went's Wrong. Please try again",
    //                'errors' => $e->getMessage(),
    //            ], 500);
    //        }
    // }

    public function verifyOtp(Request $request)
    {
        $validateInput=Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|numeric|digits:4'
            ]
        );

        if($validateInput->fails()){
            return response()->json([
                'status' => false,
                'message' => "validation error",
                'error' => $validateInput->errors()->first()
            ],401);
        }

        $otpExists = DB::table('password_resets')
                        ->where([
                            'email'=> $request->email,
                            'otp' => $request->otp
                        ])->first();
        if(!$otpExists){
            return response()->json([
                'status' => false,
                'message' => 'Please Enter a Valid Otp',
            ],404);
        }

        $optCreatedAt = Carbon::parse($otpExists->created_at);
        if($optCreatedAt->diffInMinutes(Carbon::now())>1){
            return response()->json([
                'status' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ],422);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully',
        ],200);


    }


    public function changePassword(Request $request, $id)
    {
        try{
            $validateInput=Validator::make(
                $request->all(),
                [
                    'oldPassword' => 'required',
                    'newPassword' => 'required|confirmed|min:5',
                    'newPassword_confirmation' => 'required',
                ]
            );

            if($validateInput->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication Error',
                    'errors' => $validateInput->errors()->first()
                ],404);
            }

            $user=User::find($id);
            if(!Hash::check($request->oldPassword, $user->password)){
                return response()->json([
                    'status' => false,
                    'message' => 'The Old Password does not match our record',
                ], 404);
            }

            $user->update(['password'=>Hash::make($request->newPassword)]);

            return response()->json([
                'status' => true,
                'message' =>  'Your Password Has been Changed',
             ],201);

        }
        catch (\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => "something went's Wrong. Please try again",
                'errors' => $e->getMessage(),
            ], 500);
        }

    }















}
