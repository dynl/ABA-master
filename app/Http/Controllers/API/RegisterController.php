<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    // User registration
    public function register(Request $request)
    {
        // 1. Modified Validation
        // We removed 'name' => 'required' because Flutter isn't sending it.
        // We added 'contact_number' so Laravel knows to accept it.
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'contact_number' => 'nullable|string|max:20', 
        ]);

        // 2. Generate a Name automatically
        // Since Flutter doesn't send a name, we take the part of the email before the '@'
        // Example: if email is "john@test.com", name becomes "john"
        $generatedName = explode('@', $request->email)[0];

        $user = User::create([
            'name' => $generatedName, 
            'email' => $request->email,
            'password' => $request->password, // Laravel automatically hashes this if using modern versions
            'contact_number' => $request->contact_number, // Saving the contact number
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            // 3. Changed 'access_token' to 'token' to match your Flutter code
            'token' => $token, 
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200); // Changed to 200 to ensure Flutter reads it easily
    }

    // User login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            // Changed 'access_token' to 'token' here too
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    // User logout
    public function logout(Request $request)
    {
        // Check if user is actually logged in before trying to delete token
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }
}