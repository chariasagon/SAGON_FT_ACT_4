<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Import User model

class AuthController extends Controller
{
    // Show Login Form
    public function showLoginForm()
    {
        return view('login');
    }

    // Handle Login
    public function login(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'email' => 'required|email', // Treat 'email' as 'username' for validation
                'password' => 'required'
            ]);

            // Rate limiting
            $key = 'login_attempts:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json(['error' => 'Too many login attempts. Try again later.'], 429);
            }

            // Check if user exists in the database (modify 'email' to 'username' here)
            $user = User::where('username', $request->email)->first(); // Assuming 'username' column
            if (!$user) {
                // If the user doesn't exist, return a message to register
                return response()->json(['error' => 'User not found. Please register first.'], 404);
            }

            // Try to authenticate the user using 'username' (which you are using for email)
            if (Auth::attempt([
                'username' => $request->email, // Use 'username' as login field
                'password' => $request->password
            ])) {
                RateLimiter::clear($key);  // Clear rate limiting if login is successful

                // Log the login event
                LoginLog::create([
                    'user_id' => Auth::id(),
                    'login_method' => session('login_method', 'traditional'),
                    'is_successful' => true,
                    'ip_address' => $request->ip(),
                    'details' => 'User successfully logged in.',
                ]);

                // Redirect to the analytics dashboard after successful login
                return response()->json([
                    'message' => 'Login successful',
                    'redirect' => route('dashboard-analytics') // Use the updated route here
                ], 200);
            }

            // Failed login attempt
            RateLimiter::hit($key, 60);

            LoginLog::create([
                'user_id' => null,
                'login_method' => 'traditional',
                'is_successful' => false,
                'ip_address' => $request->ip(),
                'details' => 'Failed login attempt for username: ' . $request->email,
            ]);

            return response()->json(['error' => 'Invalid credentials'], 401);

        } catch (\Exception $e) {
            // Capture the exception with line number and file
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Display error with line number and file name
            return response()->json([
                'error' => 'Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine()
            ], 500);
        }
    }

 // Logout function
public function logout(Request $request)
{
    if (Auth::check()) {
        // Log the logout action in the database
        LoginLog::create([
            'user_id' => Auth::id(),
            'login_method' => session('login_method', 'traditional'),  // Session value for login method
            'is_successful' => true,  // Successful logout
            'ip_address' => $request->ip(),
            'details' => 'User logged out successfully.',
        ]);
    }

    Auth::logout();  // Log the user out
    session()->flush();  // Clear session data

    // Redirect back to login page with a success message
    return redirect()->route('login')->with('success', 'You have been logged out.');
}
}