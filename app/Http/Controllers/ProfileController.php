<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

   
    public function index()
    {
        $user = auth()->user();
        Log::info("User profile retrieved", ['user_id' => $user->user_id]);

        return $this->jsonResponse($user, 'User profile retrieved successfully.');
    }


    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->user_id,
            'phone' => 'nullable|string|unique:users,phone,' . $user->user_id,
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);
        Log::info("User profile updated", ['user_id' => $user->user_id]);

        return $this->jsonResponse($user, 'Profile updated successfully.');
    }

   
    public function favorites()
    {
        $favorites = auth()->user()->favorites;
        Log::info("User favorites retrieved", ['user_id' => auth()->user()->user_id]);

        return $this->jsonResponse($favorites, 'User favorites retrieved successfully.');
    }

   
    public function addFavorite(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer',
            'item_type' => 'required|in:flight,hotel,car',
        ]);

        auth()->user()->favorites()->create($validated);
        Log::info("Favorite added", ['user_id' => auth()->user()->user_id, 'item' => $validated]);

        return $this->jsonResponse(null, 'Item added to favorites.');
    }

   
    public function removeFavorite($id)
    {
        $favorite = auth()->user()->favorites()->find($id);

        if (!$favorite) {
            Log::warning("Favorite not found", ['user_id' => auth()->user()->user_id, 'favorite_id' => $id]);
            return $this->notFoundResponse('Favorite not found.');
        }

        $favorite->delete();
        Log::info("Favorite removed", ['user_id' => auth()->user()->user_id, 'favorite_id' => $id]);

        return $this->jsonResponse(null, 'Favorite removed successfully.');
    }

 
    protected function jsonResponse($data = [], $message = '', $status = 200, $code = null)
    {
        return response()->json([
            'status' => $status === 200 ? 'success' : 'error',
        'message' => $message,
        'data' => $data
        ], $status, [], JSON_UNESCAPED_UNICODE);
    }


    protected function notFoundResponse($message = 'Resource not found', $code = 404)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
