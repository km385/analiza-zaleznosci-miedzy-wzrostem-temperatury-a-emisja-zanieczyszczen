<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register'
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = Auth::login($user);

        

        return response()->json([
            'status' => 'success',
            'message' => 'User Registered Successfully',
            'user' => $user,
            'token' => $token,
            'redirect' => '/'
        ]);
    }

    public function login(Request $request)
    {
        
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login Failed'
            ]);
        }

        
        Cookie::queue('jwt_token', $token, 60);
        
        
        return response()->json([
            'status' => 'success',
            'message' => 'Login Successfully',
            'token' => $token,
            'redirect' => '/'
        ]);
    }

    public function logout(Request $request)
    {
        
        Cookie::queue(Cookie::forget('jwt_token')); 

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
            'redirect' => '/'
        ]);
    }
}
