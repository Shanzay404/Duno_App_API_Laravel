<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function signup(Request $request){
      try {
          //validate User
          $validateUser = Validator::make(
            $request->all(), 
            [
                "firstname" => 'string|nullable',
                "lastname" => 'string|nullable',
                "contact" => 'numeric|min:11|max:11|nullable',
                "gender" => 'string|nullable',
                "email" => "required|email|unique:users,email",
                "password" => "required|min:5|confirmed",
                "password_confirmation" => "required",
            ]
        );
        // if the validaton is fails
        if($validateUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                // 'errors' => $validateUser->errors()->all()
                'errors' => $validateUser->errors()->first()
            ],401);
        }
        //if the user is authenticated
        $user = User::create([
            'email' => $request->email,
            'password' => $request->password
        ]);
        // create token for signup user
        $registerToken = $user->createToken('Signup Token')->plainTextToken;
        //send response for successfully created user
        return response()->json([
            'status' => true,
            'message' => 'User has been created',
            'token' => $registerToken,
            'user' => $user
        ],201);
      } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went\'s wrong, please try again',
            'errors' => $e->getMessage()
        ],500);
      }
    }
    public function login(Request $request){
        try{
            //validate User
            $validateUser = Validator::make(
                $request->all(), 
                [
                    "email" => "required|email",
                    "password" => "required",
                ]
            );
            // if the validaton is fails
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Authentication Fails',
                    'errors' => $validateUser->errors()->first()
                ],404);
            }
    
            if(Auth::attempt(['email'=>$request->email,'password'=>$request->password ])){
                $authUser = Auth::user();
                return response()->json([
                    'status' => true,
                    'message' => 'User Login Successfully',
                    'user' => $authUser,
                    'token' => $authUser->createToken('Login Token')->plainTextToken,
                ],201);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Credentials! Email or password doesn\'t match',
                ],401);
            }
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went\'s wrong, please try again',
                'errors' => $e->getMessage()
            ],500);
          }
    }

    public function logout(Request $request){
       try{
           $user = $request->user();
           $user->tokens()->delete();
   
           return response()->json([
               'status' => true,
               'message' => 'User has been Logged Now',
           ],201);

       } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went\'s wrong, please try again',
            'errors' => $e->getMessage()
        ],500);
      }
    }

    public function Edit(Request $request, $id)
    {
     try 
        {
            $validateUser = Validator::make(
                $request->all(), 
                [
                    "firstname" => 'string|nullable',
                    "lastname" => 'string|nullable',
                    "contact" => 'numeric|digits_between:10,15|nullable',
                    "gender" => 'string|nullable',
                    "email" => [
                        "required",
                        "email",
                        Rule::unique('users', 'email')->ignore($id),
                    ],

                ]
            );
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validateUser->errors()->first()
                ],401);
            }

            $user=User::find($id);
            $user->update([
                'firstname'=>$request->firstname,
                'lastname'=>$request->lastname,
                'email'=>$request->email,
                'contact'=>$request->contact,
                'gender'=> $request->gender,
            ]);

            return response()->json([
                'status' => true,
                'message' => "Profile Updated Successfully",
                'user' => $user
            ],201);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went\'s wrong, please try again',
                'errors' => $e->getMessage()
            ],500);
        }
    }
}
