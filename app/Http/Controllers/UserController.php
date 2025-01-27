<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request)
    {
        Log::info('Fetching user list', ['user' => auth()->user()->user_id ?? 'Guest']);
        $this->authorize('viewAny', User::class);
        $query = User::query();
        if ($request->filled('name'))
        {$query->where('name', 'like', '%' . $request->name . '%');}

        if ($request->filled('email'))
        {$query->where('email', 'like', '%' . $request->email . '%');}

        if ($request->filled('phone'))
        {$query->where('phone', 'like', '%' . $request->phone . '%');}

        if ($request->filled('role'))
        {$query->where('role', $request->role);}

        if ($request->filled('country_id'))
        {$query->where('country_id', $request->country_id);}

        $users = $query->paginate($request->input('per_page', 10));
        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    public function profile()
    {
        $user = auth()->user();
        if (!$user) {
            Log::warning('Unauthorized access attempt to profile');
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        Log::info("User profile retrieved successfully", ['user_id' => $user->user_id]);
        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);
        Log::info("Viewing user profile", ['user_id' => $user_id]);
        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    public function store(Request $request)
    {
        Log::info('Attempting to register a new user', ['request_data' => $request->all()]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'nullable|exists:countries,country_id',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('user_images', 'public');
        }

        $user = User::create(array_merge($validated, [
            'password' => bcrypt($validated['password']),
        ]));
        Log::info("User registered successfully", ['user_id' => $user->user_id]);
        return response()->json(['status' => 'success', 'data' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        Log::info('Updating user profile', ['user_id' => $user_id]);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
            'password' => 'nullable|string|min:6',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'nullable|exists:countries,country_id',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $validated['profile_picture'] = $request->file('profile_picture')->store('user_images', 'public');
            }

            $user->update(array_merge($validated, [
                'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
            ]));

            DB::commit();
            Log::info("User updated successfully", ['user_id' => $user_id]);
            return response()->json(['status' => 'success', 'data' => $user], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("User update failed", ['user_id' => $user_id, 'error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update user'], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        Log::info('Deleting user', ['user_id' => $user_id]);

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        Log::info("User deleted successfully", ['user_id' => $user_id]);

        return response()->json(['status' => 'success', 'message' => 'User deleted successfully'], 200);
    }
}
