<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
    //Confi middleware for controller
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    //Login fuction
    //Check remember token -> check account -> response JSON
    public function login(Request $request)
    {
        //Check remember token
        //True
        if (auth()->user()) {
            //Check account
            $user = auth()->user();
            //Active
            if ($user['status'] == true) {
                //refresh a new token and response JSON
                $newtoken = auth()->refresh();
                return $this->respondWithToken($newtoken);
            }
            //Deactive
            else {
                return response()->json(['error' => 'token is deactive','action'=>'login'], 401);
            }
        }
        //False
        else {
            //Check account by attempt (email, password, status)
            $credentials['email'] = $request['Email'];
            $credentials['password'] = $request['Password'];
            $credentials['status'] = true;
            //Deactive
            if (!$token = auth()->setTTL(21600)->attempt($credentials)) {
                //response
                return response()->json(['error' => "account is not correct",'action'=>'login'], 401);
            }
            //Active
            //response
            return $this->respondWithToken($token);
        }
    }

    //Construct of JSON
    protected function respondWithToken($token)
    {
        $user = auth()->user();
        return response()->json([
            'Name' => $user->name,
            'Email'=>$user->email,
            'Role'=>$user->role,
            'access_token' => $token,
        ]);
    }

    //Logout function
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    //Get user from token
    public function me()
    {
        return new UserResource(auth()->user());
    }
}
