<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PassportController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Fetching passports', ['user_id' => auth()->id()]);

        $this->authorize('viewAny', Passport::class);

        $query = Passport::with('user');

        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->input('is_verified'));
            Log::info('Filtering by verification status', ['is_verified' => $request->input('is_verified')]);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
            Log::info('Filtering by user ID', ['user_id' => $request->input('user_id')]);
        }

        $perPage = $request->input('per_page', 10);
        $passports = $query->paginate($perPage);

        Log::info('Passports fetched successfully', ['count' => $passports->count()]);

        return response()->json(['status' => 'success', 'data' => $passports], 200);
    }

    public function store(Request $request)
    {
        Log::info('Attempting to store passport', ['user_id' => auth()->id()]);

        $this->authorize('create', Passport::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'passport_number' => 'required|unique:passports,passport_number',
            'full_name' => 'required|string|max:255',
            'country_of_issue' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'passport_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('passport_image')) {
            $validated['passport_image'] = $request->file('passport_image')->store('images/passports', 'public');
            Log::info('Passport image uploaded successfully');
        }

        $passport = Passport::create($validated);

        Log::info('Passport created successfully', ['passport_id' => $passport->passport_id]);

        return response()->json(['status' => 'success', 'data' => $passport], 201);
    }

    public function show($id)
    {
        Log::info("Fetching passport details", ['passport_id' => $id]);

        $passport = Passport::with('user')->findOrFail($id);

        $this->authorize('view', $passport);

        Log::info("Passport details retrieved successfully", ['passport_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $passport], 200);
    }

    public function update(Request $request, $id)
    {
        Log::info("Attempting to update passport", ['passport_id' => $id]);

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
            Log::info('Passport image updated successfully');
        }

        $passport->update($validated);

        Log::info("Passport updated successfully", ['passport_id' => $id]);

        return response()->json(['status' => 'success', 'data' => $passport], 200);
    }

    public function destroy($id)
{
    try {
        $userId = auth()->user()->user_id;
        Log::info('Attempting to delete passport', ['user_id' => $userId, 'passport_id' => $id]);

        $passport = Passport::findOrFail($id);
        $this->authorize('delete', $passport);

        if ($passport->passport_image) {
            if (Storage::disk('public')->exists($passport->passport_image)) {
                Storage::disk('public')->delete($passport->passport_image);
            }
        }

        $passport->delete();

        Log::info('Passport deleted successfully', ['user_id' => $userId, 'passport_id' => $id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Passport deleted successfully'
        ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::warning('Passport not found', ['user_id' => $userId, 'passport_id' => $id]);
        return response()->json([
            'status' => 'error',
            'message' => 'Passport not found'
        ], 404);
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        Log::error('Unauthorized passport deletion attempt', ['user_id' => $userId, 'passport_id' => $id]);
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized to delete passport'
        ], 403);
    } catch (\Exception $e) {
        Log::error('Error deleting passport', [
            'user_id' => $userId,
            'passport_id' => $id,
            'error_message' => $e->getMessage()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete passport'
        ], 500);
    }
}


    

    public function verify($id)
    {
        Log::info("Attempting to verify passport", ['passport_id' => $id]);

        $passport = Passport::findOrFail($id);

        $this->authorize('verify', Passport::class);

        $passport->update(['is_verified' => true]);

        Log::info("Passport verified successfully", ['passport_id' => $id]);

        return response()->json(['status' => 'success', 'message' => 'Passport verified', 'data' => $passport], 200);
    }
}
