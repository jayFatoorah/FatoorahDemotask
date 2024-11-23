<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request, $guard)
    {
        $user = null;
        if ($guard === 'admin') {
            $user = \App\Models\User::where('email', $request->email)->where('role', 'admin')->first();
        } elseif ($guard === 'user') {
            $user = \App\Models\User::where('email', $request->email)->where('role', 'user')->first();
        } else {
            return response()->json(['error' => 'Invalid guard type'], 400);
        }
    
        // Check if user exists and validate the password
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    
        // Create a Sanctum token for the user
        $token = $user->createToken(config('app.name'), ['role:'.$guard])->plainTextToken;
    
        return response()->json(['token' => $token]);

    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',  // Default to user
        ]);

        return response()->json(['message' => 'User registered successfully']);
    }

    public function logout(Request $request)
    {
        if(Auth::user()){
            Auth::user()->tokens->each(function ($token) {
                $token->delete();
            });    
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return response()->json(['message' => 'Logged out successfully']);
    }
}
