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
        try {
            \Log::info('Login attempt:', ['email' => $request->email, 'role' => $request->role]);
    
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6',
                'role' => 'required|in:user,admin',
            ]);
    
            if ($validator->fails()) {
                \Log::warning('Validation failed:', ['errors' => $validator->errors()]);
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user) {
                \Log::warning('User not found:', ['email' => $request->email]);
                return response()->json(['message' => 'Invalid credentials or role'], 401);
            }
    
            if ($user->role !== $request->role) {
                \Log::warning('Role mismatch:', ['user_id' => $user->id, 'expected' => $request->role, 'actual' => $user->role]);
                return response()->json(['message' => 'Invalid credentials or role'], 401);
            }
    
            if (!Hash::check($request->password, $user->password)) {
                \Log::warning('Password mismatch:', ['user_id' => $user->id]);
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            \Log::info('Login successful:', ['user_id' => $user->id]);
    
            return response()->json([
                'message' => 'Login successful!',
                'role' => $user->role,
                'user' => $user,
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('Login error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
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
