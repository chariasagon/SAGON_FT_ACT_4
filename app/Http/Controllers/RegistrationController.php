<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    
    public function showForm()
    {
        return view('registration');
    }

     // New method to show the list of registrations
     public function list()
    {
        // Fetch all users (registrations)
        $users = User::all();

        // Return the view with the user data
        return view('registration.list', compact('users'));
    }

 
    public function register(Request $request)
    {
        try {
            // Validate the form fields
            $request->validate([
                'username'        => 'required|email', // Ensure the username is an email
                'first_name'      => 'required',
                'last_name'       => 'required',
                'password'        => 'required',
                'retype_password' => 'required',
            ]);
    
            // Check if the username already exists in the database
            if (User::where('username', $request->username)->exists()) {
                return response()->json(['error' => 'The username is already taken. Please choose another one.'], 400);
            }
    
            // Check if passwords match
            if ($request->password !== $request->retype_password) {
                throw new \Exception('Password did not match');
            }
    
            // Check password length
            if (strlen($request->password) < 8) {
                throw new \Exception('Password must be greater or equal to 8 characters');
            }
    
            // Create the user
            $User = [
                'username'   => $request->username,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'password'   => Hash::make($request->password),
            ];
    
            User::create($User);
    
            return response()->json(['success' => 'Registration successful!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}    