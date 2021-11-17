<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $validatedData['password'] = Hash::make($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken], 201);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['message' => 'This User does not exist, check your details'], 400);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
    public function hello(Request $request){
        print 'we received request here';
    }
    public function Success(Request $request){
        echo 'we received request here'. $request;
        $configuration =array("client_id"=> "1000.7HUGW9O3WU65V5O18Z75TVTLL9QWXS","client_secret"=>"542dcac0ea8813a1368662173453d4d5f8ff651bae","redirect_uri"=>"http://127.0.0.1:8000/api/Tokenrefresh","currentUserEmail"=>"saeedbuttfreelance@gmai.com");
        ZCRMRestClient::initialize($configuration);
        $oAuthClient = ZohoOAuth::getClientInstance(); 
        $refreshToken = "1000.2d4a5e497325b37fffc656e6b07a4aae.5319d0baf8b883fb22932708fdfdb37d"; 
        $userIdentifier = "saeedbutt320@gmail.com"; 
        $oAuthClient->generateAccessTokenFromRefreshToken($refreshToken,$userIdentifier);
        return $oAuthClient->generateAccessTokenFromRefreshToken($refreshToken,$userIdentifier);

    }
}