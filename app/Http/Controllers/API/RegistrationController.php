<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function register(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'contact_number' => 'required|string|max:20', // Updated to match the frontend and database
        'role' => 'required|in:user,admin',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'fail',
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_number' => $request->contact_number, // Updated to match the database
            'role' => $request->role,
        ]);

        \Log::info('User registered successfully:', ['user_id' => $user->id]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully!',
            'user' => $user,
        ], 201);
    } catch (\Exception $e) {
        \Log::error('Registration Error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status' => 'fail',
            'message' => 'Registration failed. Please try again.',
        ], 500);
    }
}
}