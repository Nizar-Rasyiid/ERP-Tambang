<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

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
    public function assignRole(Request $request, User $user){        
        $user->syncRoles([$request->role]);
        return response()->json(['message' => 'Role assigned successfully']);
    }    

    public function assignPermissions(Request $request, User $user)
    {
        $user->syncPermissions($request->permissions);
        return response()->json(['message' => 'Permissions updated successfully']);
    }

    public function getPermissions(User $user)
    {
        return response()->json($user->permissions);
    }

    // Logout Function
    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

}
