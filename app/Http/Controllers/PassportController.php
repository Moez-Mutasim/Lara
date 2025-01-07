<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PassportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Passport::class);

        $query = Passport::with('user');

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->input('is_verified'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $perPage = $request->input('per_page', 10);
        $passports = $query->paginate($perPage);

        return response()->json(['status' => 'success', 'data' => $passports], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Passport::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'passport_number' => 'required|unique:passports,passport_number',
            'full_name' => 'required|string|max:255',
            'country_of_issue' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'passport_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('passport_image')) {
            $validated['passport_image'] = $request->file('passport_image')->store('images/passports', 'public');
        }

        $passport = Passport::create($validated);

        return response()->json(['status' => 'success', 'data' => $passport], 201);
    }

    public function show($id)
    {
        $passport = Passport::with('user')->findOrFail($id);

        $this->authorize('view', $passport);

        return response()->json(['status' => 'success', 'data' => $passport], 200);
    }

    public function update(Request $request, $id)
    {
        $passport = Passport::findOrFail($id);

        $this->authorize('update', $passport);

        $validated = $request->validate([
            'passport_number' => 'nullable|unique:passports,passport_number,' . $id,
            'full_name' => 'nullable|string|max:255',
            'country_of_issue' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'passport_image' => 'nullable|image|max:2048',
            'is_verified' => 'nullable|boolean',
        ]);

        if ($request->hasFile('passport_image')) {
            if ($passport->passport_image) {
                Storage::disk('public')->delete($passport->passport_image);
            }
            $validated['passport_image'] = $request->file('passport_image')->store('images/passports', 'public');
        }

        $passport->update($validated);

        return response()->json(['status' => 'success', 'data' => $passport], 200);
    }

    public function destroy($id)
    {
        $passport = Passport::findOrFail($id);

        $this->authorize('delete', $passport);

        if ($passport->passport_image) {
            Storage::disk('public')->delete($passport->passport_image);
        }

        $passport->delete();

        return response()->json(['status' => 'success', 'message' => 'Passport deleted'], 200);
    }

    public function verify($id)
    {
        $passport = Passport::findOrFail($id);

        $this->authorize('verify', Passport::class);

        $passport->update(['is_verified' => true]);

        return response()->json(['status' => 'success', 'message' => 'Passport verified', 'data' => $passport], 200);
    }
}
