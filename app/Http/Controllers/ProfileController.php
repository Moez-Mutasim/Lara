<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the authenticated user's profile.
     */
    public function index()
    {
        $user = auth()->user();

        return $this->jsonResponse($user, 'User profile retrieved successfully.');
    }

    /**
     * Update the authenticated user's profile.
     */
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
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return $this->jsonResponse($user, 'Profile updated successfully.');
    }

    /**
     * Display the user's favorites.
     */
    public function favorites()
    {
        $favorites = auth()->user()->favorites;

        return $this->jsonResponse($favorites, 'User favorites retrieved successfully.');
    }

    /**
     * Add an item to favorites.
     */
    public function addFavorite(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer',
            'item_type' => 'required|in:flight,hotel,car',
        ]);

        auth()->user()->favorites()->create($validated);

        return $this->jsonResponse(null, 'Item added to favorites.');
    }

    /**
     * Remove an item from favorites.
     */
    public function removeFavorite($id)
    {
        $favorite = auth()->user()->favorites()->find($id);

        if (!$favorite) {
            return $this->notFoundResponse('Favorite not found.');
        }

        $favorite->delete();

        return $this->jsonResponse(null, 'Favorite removed successfully.');
    }
}
