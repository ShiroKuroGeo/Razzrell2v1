<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ContactUs;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $userData = $request->all();
        $userData['password'] = bcrypt($userData['password']);
        $userData['role'] = 'user';
        $user = User::create($userData);

        if($user) {
            return response()->json([
                'user' => $user,
                'message' => 'Login successful',
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'message' => 'User not created',
                'status' => 400
            ], 400);
        }
    }

    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');
        
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'message' => 'Login successful',
            'token' => $token,
            'status' => 200,
        ], 200);
    }

    public function contactUs(Request $request)
    {
        $contactUsData['first_name'] = $request->first_name;
        $contactUsData['last_name'] = $request->last_name;
        $contactUsData['email'] = $request->email;
        $contactUsData['phone'] = $request->phone;
        $contactUsData['event_type'] = $request->event_type;
        $contactUsData['message'] = $request->message;

        $contactUs = ContactUs::create($contactUsData);

        if($contactUs) {
            return response()->json([
                'message' => 'Contact us successful',
                'status' => 200,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Contact us not successful',
                'status' => 400,
            ], 400);
        }
    }

    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function getUserById($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function getMyInfo()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'User info retrieved successfully',
            'status' => 200,
            'data' => $user,
        ], 200);
    }

    
}
