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
            'contact' => 'required|string|max:20',
            'role' => 'required|in:user,admin', // Validate role is either 'user' or 'admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Create the user, including the role
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'contact_information' => $request->contact,
                'role' => $request->role, // Store the role in the database
            ]);

            // Automatically log in the user and generate an API token
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully!',
                'token' => $token, // Return the token
                'role' => $user->role, // Send back the role
            ], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Registration error: ' . $e->getMessage());

            return response()->json([
                'status' => 'fail',
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }
    }
}
