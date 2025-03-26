<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function Register(Request $request){
        $user = User::create([
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
    }

    public function login(Request $request) {
        $data = $request->only('email', 'password');    
        
        if (!Auth::attempt($data)) {
            return response()->json([
                'message' => 'Invalid Login',
            ]);
        }                
        $user = User::where('email', $request->email)->firstOrFail();
        // Buat token untuk pengguna
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([   
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    // Logout Function
    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

}
