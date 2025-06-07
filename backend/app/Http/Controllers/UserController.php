<?php
// is used to manage everything related to user data — not authentication (that’s for AuthController) but user profile and account management.
namespace App\Http\Controllers;

use App\Models\User; // Make sure to import the User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Ignore current user's email
            'password' => 'nullable|string|min:8|confirmed', // Password is optional for update
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * List all registered users (for admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUsers(Request $request)
    {
        // In a real application, you would add authorization checks here
        // to ensure only administrators can access this data.

        $users = User::all(); // Fetch all users

        Log::info('Fetching all users for admin view.');
        Log::info('Fetched users count: ' . $users->count());
        Log::info('Fetched users data:', $users->toArray());

        return response()->json($users);
    }

    /**
     * Delete a user (for admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user  // Route model binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(Request $request, User $user)
    {
        // In a real application, you would add authorization checks here
        // to ensure only administrators can perform this action.
        // You might also prevent deleting the currently logged-in admin user.

        $user->delete(); // Delete the user

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Update a user's profile (for admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user  // Route model binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        // In a real application, you would add authorization checks here
        // to ensure only administrators can perform this action.

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Ignore current user's email
            'password' => 'nullable|string|min:8|confirmed', // Password is optional for update
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }
} 