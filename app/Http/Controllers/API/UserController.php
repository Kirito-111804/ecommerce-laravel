<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    // User Registration
    public function register(Request $request)
    {
        // Validate the incoming request, including the role field
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'contact' => 'required|string|max:20',
            'role' => 'required|in:user,admin', // Ensure role is either 'user' or 'admin'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user, including the role
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'contact_information' => $request->contact,
            'role' => $request->role, // Store the selected role in the database
        ]);

        return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
    }

    // User Login
public function login(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:6',
        'role' => 'required|in:user,admin', // Validate role must be 'user' or 'admin'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    // Check if the user exists and if the role matches
    if (!$user || $user->role !== $request->role) {
        return response()->json(['message' => 'Invalid credentials or role'], 401);
    }

    // If password is incorrect, return an error
    if (!Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Generate the API token
    $token = $user->createToken('API Token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful!',
        'token' => $token,
        'role' => $user->role,  // Return the role in the response
        'user' => $user,
    ], 200);
}





    // User Logout
    public function logout(Request $request)
    {
        // Revoke the user's current API token
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    // Get All Registered Users
    public function index()
    {
        // Fetch all users from the database
        $users = User::all();

        // Return the users as a JSON response
        return response()->json([
            'status' => 'success',
            'users' => $users,
        ]);
    }
}
