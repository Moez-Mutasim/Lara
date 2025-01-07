<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $users = $query->paginate(10);

        return response()->json(['status' => 'success', 'data' => $users], 200);
    }

    public function profile()
    {
        $user = auth()->user();

        return $user
            ? response()->json(['status' => 'success', 'data' => $user], 200)
            : response()->json(['message' => 'Unauthorized'], 401);
    }


    public function show($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }


    public function store(Request $request)
    {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
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
    
            return response()->json(['status' => 'success','data' => $user,], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

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

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('user_images', 'public');
        }

        $user->update(array_merge($validated, [
            'password' => $request->filled('password') ? bcrypt($request->password) : $user->password,
        ]));

        return response()->json(['status' => 'success', 'data' => $user], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'User deleted successfully'], 200);
    }

}
